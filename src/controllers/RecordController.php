<?php namespace EternalSword\LPress;

use Illuminate\Routing\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\HTML;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class RecordController extends BaseController {
	public static function parseRoute($path) {
		$router = new SlugRouter($path);
		if($router->getStatusCode() == 404) {
			if($router->hasJSON()) {
				$json = new \stdClass;
				$json->status_code = 404;
				$json->reason = Lang::get('l-press::errors.invalidURL');
				return Response::json($json, 404);
			}
			return App::abort(404, Lang::get('l-press::errors.invalidURL'));
		}
		$slug_types = $router->getSlugTypes();
		if($slug_types[0] == 'record') {
			return self::getRecord($router);
		}
		if($slug_types[0] == 'record_type') {
			return self::getRecordsByRecordType($router);
		}
	}

	public static function getRecord($router, $public = TRUE) {
		$verifyAttachment = function($record) use (&$path) {
			$found = FALSE;
			foreach($record->values as $value) {
				if($value->field->slug == 'file') {
					if($record->id > 1) {
						$date = new \DateTime($value->current_revision->updated_at);
						$date = $date->format('/Y/m');
						$path .= $date;
					}
					$path .= '/' . $value->current_revision->contents;
					$found = TRUE;
					break;
				}
			}
			return $found;
		};
		$json = $router->hasJSON();
		$record = $router->getRecord();
		if($record->public != $public) {
			if($json) {
				$json = new \stdClass;
				$json->status_code = 403;
				$json->reason = Lang::get('l-press::errors.permissionError');
				return Response::json($json, 403);
			}
			else {
				return App::abort('403', Lang::get('l-press::errors.permissionError'));
			}
		}
		$record->load(
			'author',
			'publisher',
			'values.field',
			'values.current_revision.author',
			'values.current_revision.publisher'
		);
		$root_record_type = $router->getRootRecordType();
		if($json) {
			if($root_record_type->slug == 'attachments') {
				if(!$verifyAttachment($record)) {
					$json = new \stdClass;
					$json->status_code = 404;
					$json->error = Lang::get('l-press::errors.attachmentMissing');
					return Response::json($json, 404);
				}
			}
			return Response::json($record);
		}
		if($root_record_type->slug == 'attachments') {
			$site = $record->site()->first();
			$path = dirname($router->getPath());
			$attachment_config = Config::get('l-press::attachments');
			$path = $attachment_config['path'] . '/' . $site->domain . '/' . $path;
			if(!$verifyAttachment($record)) {
				return App::abort('404', Lang::get('l-press::errors.attachmentMissing'));
			}
			$asset = new AssetController;
			return $asset->getAsset($path);
		}
	}

	public static function getRecordsByRecordType($router, $public = TRUE) {
		$json = $router->hasJSON();
		$record_type = $router->getRecordType();
		$record_type->load('children');
		$record_type->load(array('symlinks' => function($query) {
			$query->where('site_id', '=', SITE);
		}));
		$symlinks = $record_type->symlinks->lists('record_id');
		$record_type->load(array('records' => function($query) use($public, $symlinks) {
			if($public) {
				$query
				->where('public', '=', TRUE)
				->where(function($query) use($symlinks) {
					$query
					->whereIn('id', count($symlinks) > 0 ? $symlinks : array(0))
					->orWhere('site_id', '=', SITE);
				})
				->orderBy('updated_at');
			}
			else {
				$query
				->whereIn('id', count($symlinks) > 0 ? $symlinks : array(0))
				->orWhere('site_id', '=', SITE)
				->orderBy('updated_at');
			}
		}));
		$record_type->records->load(
			'author',
			'publisher',
			'values.field',
			'values.current_revision.author',
			'values.current_revision.publisher'
		);
		if($json) {
			return Response::json($record_type);
		}
		$descendents = $record_type->getDescendents();
		$records = $record_type->records->all();
		foreach($descendents as $descendent) {
			$descendent->load('records');
			if(count($descendent->records) > 0) {
				$descendent->records->load(
					'author',
					'publisher',
					'values.field',
					'values.current_revision.author',
					'values.current_revision.publisher'
				);
				$records = array_merge($records, $descendent->records->all());
			}
		}
		$slugs = $router->getSlugs();
		extract(parent::prepareMake());
		$label = $record_type->label_plural;
		$original_record_type = $record_type;
		while($record_type->depth > 0) {
			try {
				return View::make(
					$view_prefix . '.collections.' . $record_type->slug,
					array(
						'domain' => DOMAIN,
						'view_prefix' => $view_prefix,
						'title' => $site['label'] . '::' . $label,
						'label' => $label,
						'slugs' => $slugs,
						'records' => $records,
						'path' => $router->getPath(),
						'record_type' => $original_record_type,
						'route_prefix' => Config::get('l-press::route_prefix')
					)
				);
			} catch (\InvalidArgumentException $e) {
				$record_type = RecordType::find($record_type->parent_id);
			}
		}
		return App::abort(404, Lang::get('l-press::errors.templateMissing'));
	}

	public static function getRecordForm() {
	}

	public static function createRecord() {
	}

	public static function createAttachmentRecord($path, $user) {
		if(!$user->hasPermission('create')) {
			return App::abort(403, Lang::get('l-press::errors.executePermissionError'));
		}
		$attachment_config = Config::get('l-press::attachments');
		$relative_path = explode($attachment_config['path'], $path);
		$segments = explode('/', $relative_path[1]);
		array_shift($segments);
		$domain = array_shift($segments);
		$site = Site::where('domain', '=', $domain)->first();
		$count = count($segments);
		$file_name = $segments[--$count];
		$record_type_slug = $segments[$count-3];
		$label = explode('.', $file_name);
		$record_type = RecordType::where('slug', '=', $record_type_slug)->first();
		$record = new Record();
		$record->label = $label[0];
		$record->slug = $label[0];
		$record->author_id = $user->id;
		$can_publish = FALSE;
		if($user->hasPermission(array('publish', 'publish-own'))) {
			$record->publisher_id = $user->id;
			$can_publish = TRUE;
		}
		$record->record_type_id = $record_type->id;
		$record->site_id = $site->id;
		if(!$record->save()) {
			return App::abort(500, Lang::get('l-press::errors.saveFailed'));
		}
		$value = new Value();
		$value->valuable_id = $record->id;
		$value->valuable_type = 'EternalSword\\LPress\\Record';
		$value->field_id = 4;
		$value->current_revision_id = 0;
		if(!$value->save()) {
			return App::abort(500, Lang::get('l-press::errors.saveFailed'));
		}
		$revision = new Revision();
		$revision->value_id = $value->id;
		$revision->author_id = $record->author_id;
		$revision->publisher_id = $record->publisher_id;
		$revision->prev_revision_id = 0;
		$revision->contents = $file_name;
		if(!$revision->save()) {
			return App::abort(500, Lang::get('l-press::errors.saveFailed'));
		}
		if($can_publish) {
			$value->current_revision_id = $revision->id;
			if(!$value->save()) {
				return App::abort(500, Lang::get('l-press::errors.saveFailed'));
			}
			$record->public = TRUE;
			if(!$record->save()) {
				return App::abort(500, Lang::get('l-press::errors.saveFailed'));
			}
		}
		$record->load(
			'author',
			'publisher',
			'values.field',
			'values.current_revision.author',
			'values.current_revision.publisher'
		);
		return $record;
	}
}
