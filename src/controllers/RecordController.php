<?php namespace EternalSword\LPress;

use Illuminate\Routing\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\HTML;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class RecordController extends BaseController {
	public static function parseRoute($path) {
		$route = BaseController::slugsToRoute($path);
		if($route->throw404) {
			App::abort(404);
		}
		if($route->slug_types[0] == 'record') {
			/* fill this out when at testing point */
		}
		if($route->slug_types[0] == 'record_type') {
			return self::getRecordsByRecordType($route);
		}
	}

	public static function getRecordsByRecordType($route) {
		$json = $route->json;
		$record_type = $route->record_type;
		$slugs = $route->slugs;
		if($record_type->records->count() > 0) {
			$record_type->records()->load('author');
			$record_type->records()->load('publisher');
			$record_type->records()->load('values');
			$record_type->records()->values()->load(
				array(
					'revisions' => function($query) {
						$query->where('id', '=', $value->current_revision_id);
					}
				)
			);
		}
		if($json) {
			return Response::json($record_type);
		}
		else {
			extract(parent::prepareMake());
			$label = $record_type->label;
			return View::make($view_prefix . '.collections', 
				array(
					'domain' => DOMAIN,
					'view_prefix' => $view_prefix,
					'title' => $site[0]['label'] . '::' . $label,
					'label' => $label,
					'slugs' => $slugs,
					'record_type' => $record_type,
					'route_prefix' => Config::get('l-press::route_prefix')
				)
			);
		}
	}
}
