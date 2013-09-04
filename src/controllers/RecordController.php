<?php namespace EternalSword\LPress;

use Illuminate\Routing\Controllers\Controller;
use Illuminate\Support\Facades\HTML;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class RecordController extends BaseController {
	public static function getRecordsByRecordType($record_type, $json = FALSE) {
		if($record_type->records->count() > 0) {
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
			return View::make($view_prefix . '.collections', 
				array(
					'domain' => DOMAIN,
					'view_prefix' => $view_prefix,
					'title' => $site[0]['label'],
					'route_prefix' => Config::get('l-press::route_prefix')
				)
			);
		}
	}
}
