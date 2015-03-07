<?php namespace EternalSword\Lib;

class SlugRouter {
	function __construct($path = '/') {
		$route_prefix = (new PrefixGenerator)->getPrefix();
		if(!empty($route_prefix)) {
			$real_path = explode($route_prefix, $path);
			$this->path = $real_path[1];
		}
		else {
			$this->path = $path;
		}
		$segments = preg_split('@/@', $this->getPathWithoutJSON(), NULL, PREG_SPLIT_NO_EMPTY);
		$this->segments = $segments;
		$base_segment = array_shift($segments);
		$base_type = $this->getBaseType($base_segment);
		if($base_type === FALSE) {
			$this->status_code = 404;
			return;
		}
		$slugs = array($base_segment);
		$slug_types = array('record_type');
		if(!empty($segments)) {
			$base_slug = array_pop($segments);
			if(!empty($segments)) {
				if($this->verifySegments($segments, $slugs, $slug_types, $base_type) === FALSE) {
					$this->status_code = 404;
					return;
				}
			}
			if(!$this->verifyBaseSlug($base_slug, $base_type, $slug_types)) {
				$this->status_code = 404;
				return;
			}
		}
		$this->slug_types = $slug_types;
		$this->slugs = $slugs;
	}

	protected $path;
	protected $segments;
	protected $status_code = 200;
	protected $return_json = FALSE;
	protected $record_type;
	protected $root_record_type;
	protected $record;
	protected $slug_types;
	protected $slugs;

	protected function getPathWithoutJSON() {
		$path = explode('.', $this->path);
		if(count($path) > 1 && $path[1] == 'json') {
			$this->return_json = TRUE;
			return $path[0];
		}
		return $this->path;
	}

	protected function getBaseType($slug) {
		$record_type = RecordType::where('slug', '=', $slug)
		->where('depth', '=', 1)
		->first();
		if(count($record_type) > 0) {
			$this->record_type = $record_type;
			$this->root_record_type = RecordType::find($record_type->parent_id);
			return $record_type;
		}
		return FALSE;
	}

	protected function verifyBaseSlug($slug, $base_type, &$slug_types) {
		$record = Record::where('site_id', '=', SITE)
		->where('record_type_id', '=', $base_type->id)
		->where('slug', '=', $slug)
		->first();
		if(count($record) > 0) {
			array_unshift($slug_types, 'record');
			$this->record = $record;
			return TRUE;
		}
		$symlink = Symlink::where('site_id', '=', SITE)
		->where('record_type_id', '=', $base_type->id)
		->where('slug', '=', $slug)
		->first();
		if(count($symlink) > 0) {
			array_unshift($slug_types, 'record');
			$this->record = $symlink->record;
			return TRUE;
		}
		$record_type = RecordType::where('slug', '=', $slug)
		->where('parent_id', '=', $base_type->id)
		->first();
		if(count($record_type) > 0) {
			array_unshift($slug_types, 'record_type');
			$this->record_type = $record_type;
			return TRUE;
		}
		return FALSE;
	}

	protected function verifySegments($segments, &$slug_types, &$slugs, &$base_type) {
		$base_index = count($segments) - 1;
		$slug_types = array();
		$slugs = array();
		for($i=$base_index;$i>=0;$i--) {
			$slug = $segments[$i];
			$record_type = RecordType::where('slug', '=', $slug)->first();
			if(count($record_type) === 0) {
				$this->status_code = 404;
				return FALSE;
			}
			$parent = RecordType::find($record_type->parent_id);
			if($i > 0) {
				if($parent->depth > 0 && $parent->slug != $segments[$i-1]) {
					return FALSE;
				}
			}
			if($i == $base_index) {
				$base_type = $record_type;
			}
			array_unshift($slug_types, 'record_type');
			array_unshift($slugs, $slug);
		}
		return TRUE;
	}

	public function getStatusCode() {
		return $this->status_code;
	}

	public function getSlugs() {
		return $this->slugs;
	}

	public function getSlugTypes() {
		return $this->slug_types;
	}

	public function getPath() {
		return $this->path;
	}

	public function hasJSON() {
		return $this->return_json;
	}

	public function getRootRecordType() {
		return $this->root_record_type;
	}

	public function getRecordType() {
		return $this->record_type;
	}

	public function getRecord() {
		return $this->record;
	}

}
