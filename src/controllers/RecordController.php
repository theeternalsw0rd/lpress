<?php namespace EternalSword\LPress;

use Illuminate\Routing\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\HTML;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class RecordController extends BaseController {
	public static function parseRoute($path) {
		$route = BaseController::slugsToRoute($path);
		$route->path = $path;
		if($route->throw404) {
			if($route->json) {
				$json = new \stdClass;
				$json->code = 404;
				$json->reason = 'Could not locate object from path.';
				return Response::json($json, 404);
			}
			return App::abort(404, 'Could not locate object from path.');
		}
		if($route->slug_types[0] == 'record') {
			return self::getRecord($route);
		}
		if($route->slug_types[0] == 'record_type') {
			return self::getRecordsByRecordType($route);
		}
	}

	public static function getRecord($route) {
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
		$json = $route->json;
		$record = $route->record;
		$record->load(
			'author',
			'publisher',
			'values.field',
			'values.current_revision.author',
			'values.current_revision.publisher'
		);
		if($json) {
			if($route->root_record_type->slug == 'attachments') {
				if(!$verifyAttachment($record)) {
					$json = new \stdClass;
					$json->code = 404;
					$json->reason = 'Record was found, but associated value is missing.';
					return Response::json($json, 404);
				}
			}
			return Response::json($record);
		}
		if($route->root_record_type->slug == 'attachments') {
			$site = Site::find(SITE);
			$path = dirname($route->path);
			$attachment_config = Config::get('l-press::attachments');
			$path = $attachment_config['path'] . '/' . $site->domain . '/' . $path;
			if(!$verifyAttachment($record)) {
				App::abort('404', 'Record was found, but associated value is missing.');
			}
			$asset = new AssetController;
			return $asset->getAsset($path);
		}
	}

	public static function getRecordsByRecordType($route) {
		$json = $route->json;
		$record_type = $route->record_type;
		$record_type->load('records');
		if(count($record_type->records) > 0) {
			$record_type->records->load(
				'author',
				'publisher',
				'values.field',
				'values.current_revision.author',
				'values.current_revision.publisher'
			);
		}
		if($json) {
			return Response::json($record_type);
		}
		$slugs = $route->slugs;
		extract(parent::prepareMake());
		$label = $record_type->label_plural;
		$original_record_type = $record_type;
		while($record_type->depth > 0) {
			try {
				return View::make($view_prefix . '.collections.' . $record_type->slug, 
					array(
						'domain' => DOMAIN,
						'view_prefix' => $view_prefix,
						'title' => $site['label'] . '::' . $label,
						'label' => $label,
						'slugs' => $slugs,
						'path' => $route->path,
						'record_type' => $original_record_type,
						'route_prefix' => Config::get('l-press::route_prefix')
					)
				);
			} catch (\InvalidArgumentException $e) {
				$record_type->load('parent_type');
				$record_type = $record_type->parent_type;
			}
		}
		return App::abort(404, 'Could not locate object from path.');
	}

	public static function getRecordForm() {
	}

	public static function createRecord() {
	}

	public static function createAttachmentRecord($path) {
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
		$user = Auth::user();
		$record = new Record();
		$record->label = $label[0];
		$record->slug = $label[0];
		$record->author_id = $user->id;
		$record->record_type_id = $record_type->id;
		$record->site_id = $site->id;
		if(!$record->save()) {
			return App::abort(500, 'Could not save record to database.');
		}
		$value = new Value();
		$value->valuable_id = $record->id;
		$value->valuable_type = 'EternalSword\\LPress\\Record';
		$value->field_id = 4;
		$value->current_revision_id = 0;
		if(!$value->save()) {
			return App::abort(500, 'Could not save value to database.');
		}
		$revision = new Revision();
		$revision->value_id = $value->id;
		$revision->author_id = $user->id;
		$revision->prev_revision_id = 0;
		$revision->contents = $file_name;
		if(!$revision->save()) {
			return App::abort(500, 'Could not save revision to database.');
		}
		// move following code to permissioned area when fleshed out
		$value->current_revision_id = $revision->id;
		if(!$value->save()) {
			return App::abort(500, 'Could not save current revision to value in database.');
		}
	}
}
