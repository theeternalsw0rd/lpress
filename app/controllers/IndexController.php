<?php namespace EternalSword\LPress;

use Illuminate\Routing\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\HTML;
use Illuminate\Support\Facades\View;

class IndexController extends BaseController {
	public function getIndex() {
		extract(parent::prepareMake());	
		return View::make($view_prefix . '.index', 
			array(
				'domain' => DOMAIN,
				'view_prefix' => $view_prefix,
				'title' => $site[0]['label'],
				'route_prefix' => Config::get('l-press::route_prefix')
			)
		);
	}
}
