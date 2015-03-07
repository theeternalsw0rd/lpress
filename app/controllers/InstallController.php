<?php namespace EternalSword\LPress;

use Illuminate\Routing\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class InstallController extends BaseController {
	public function getInstaller() {
		extract(parent::prepareMake());	
		$user = Auth::user();
		if($user && $user->username == 'lpress') {
			$route_prefix = (new PrefixGenerator)->getPrefix();
			$dashboard_prefix = '+' . Config::get('lpress::settings.dashboard_route');
			$form_url = $route_prefix . $dashboard_prefix . '/users/1';
			return View::make($view_prefix . '.dashboard.installer',
				array(
					'view_prefix' => $view_prefix,
					'title' => Lang::get('l-press::titles.newModel', array('model_basename' => 'User')),
					'install' => TRUE,
					'form_url' => $form_url
				)
			);
		}
		if($user && $user->username != 'lpress') {
			return Redirect::route('lpress-dashboard')->with(
				'std_errors',
				array(Lang::get('l-press::errors.applicationAlreadyInstalled'))
			);
		}
	}
}
