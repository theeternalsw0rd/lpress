<?php namespace EternalSword\LPress;

class SlugRouter {
	function __construct($path = '/') {
		$this->path = $path;
	}

	protected $path;

	public function getRoute() {
		$path = $this->path;
		$route = new \stdClass;
		$route->throw404 = FALSE;
		$route->json = FALSE;
		$route_prefix = (new PrefixGenerator)->getPrefix();
		if(!empty($route_prefix)) {
			$real_path = explode($route_prefix, $path);
			$real_path = $real_path[1];
		}
		else {
			$real_path = $path;
		}
		$segments = preg_split('@/@', $real_path, NULL, PREG_SPLIT_NO_EMPTY);
		$slugs = array();
		$slug_types = array();
		$last_index = count($segments) - 1;
		$last_slug = $segments[$last_index];
		$last_slug = explode('.', $last_slug);
		if(count($last_slug) > 1 && $last_slug[1] == 'json') {
			$segments[$last_index] = $last_slug[0];
			$route->json = TRUE;
		}
		if($last_index > 0) {
			$base_index = $last_index - 1;
			// process base path first since last item has more complex logic
			for($i=$base_index;$i>=0;$i--) {
				$slug = $segments[$i];
				$record_type = RecordType::where('slug', '=', $slug)->first();
				if(count($record_type) === 0) {
					$route->throw404 = TRUE;
					break;
				}
				$parent = RecordType::find($record_type->parent_id);
				if($i > 0) {
					if($parent->depth > 0 && $parent->slug != $segments[$i-1]) {
						$route->throw404 = TRUE;
					}
				}
				else {
					$route->root_record_type = $parent;
				}
				if($i == $base_index) {
					$base_type = $record_type;
				}
				array_push($slug_types, 'record_type');
				array_push($slugs, $slug);
			}
			if(!$route->throw404) {
				$found = FALSE;
				$slug = $segments[$last_index];
				$record_type = RecordType::where('slug', '=', $slug)
				->where('parent_id', '=', $base_type->id)
				->first();
				if(count($record_type) > 0) {
					array_unshift($slug_types, 'record_type');
					array_unshift($slugs, $slug);
					$route->record_type = $record_type;
					$found = TRUE;
				}
				if(!$found) {
					$record = Record::where('site_id', '=', SITE)
					->where('record_type_id', '=', $base_type->id)
					->where('slug', '=', $slug)
					->first();
					if(count($record) > 0) {
						array_unshift($slug_types, 'record');
						array_unshift($slugs, $slug);
						$found = TRUE;
						$route->record = $record;
					}
				}
				if(!$found) {
					$symlink = Symlink::where('site_id', '=', SITE)
					->where('record_type_id', '=', $base_type->id)
					->where('slug', '=', $slug)
					->first();
					if(count($symlink) > 0) {
						array_unshift($slug_types, 'record');
						array_unshift($slugs, $slug);
						$found = TRUE;
						$route->record = $symlink->record;
					}
				}
				if($found) {
					$route->slug_types = $slug_types;
					$route->slugs = $slugs;
				}
				else {
					$route->throw404 = TRUE;
				}
			}
		}
		else {
			$found = FALSE;
			$slug = $segments[$last_index];
			$record_type = RecordType::where('slug', '=', $slug)
			->where('depth', '=', 1)
			->first();
			if(count($record_type) > 0) {
				array_push($slug_types, 'record_type');
				array_push($slugs, $slug);
				$route->record_type = $record_type;
				$found = TRUE;
			}
			if($found) {
				$route->slug_types = $slug_types;
				$route->slugs = $slugs;
			}
			else {
				$route->throw404 = TRUE;
			}
		}
		return $route;
	}
}
