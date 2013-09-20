<?php namespace EternalSword\LPress;

use Illuminate\Routing\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class InstallController extends BaseController {
	public function getInstaller() {
		extract(parent::prepareMake());	
		$user = Auth::user();
		if($user && $user->username == 'lpress') {
			return View::make($view_prefix . '.admin.create_user',
				array(
					'view_prefix' => $view_prefix,
					'title' => 'Create User',
					'install' => TRUE
				)
			);
		}
	}
}
