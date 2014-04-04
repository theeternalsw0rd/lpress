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
			return View::make($view_prefix . '.dashboard.create_user',
				array(
					'view_prefix' => $view_prefix,
					'title' => Lang::get('l-press::titles.newModel', array('model_basename' => 'User')),
					'install' => TRUE
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
