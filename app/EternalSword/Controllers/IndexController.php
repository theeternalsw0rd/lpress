<?php namespace App\EternalSword\Controllers;

use Illuminate\Routing\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\HTML;
use Illuminate\Support\Facades\View;
use GrahamCampbell\HTMLMin\Facades\HTMLMin;

class IndexController extends BaseController {
	public function getIndex() {
		extract(parent::prepareMake());	
		return HTMLMin::live(View::make($view_prefix . '.index', 
			array(
				'domain' => DOMAIN,
				'view_prefix' => $view_prefix,
				'title' => $site[0]['label'],
				'route_prefix' => Config::get('lpress::settings.route_prefix')
			)
		));
	}
}
