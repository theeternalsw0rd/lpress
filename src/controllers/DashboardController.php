<?php namespace EternalSword\LPress;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class DashboardController extends BaseController {
	public static function getDashboard() {
		extract(parent::prepareMake());	
		$user = Auth::user();
		return View::make($view_prefix . '.dashboard.index',
			array(
				'view_prefix' => $view_prefix,
				'title' => 'Dashboard',
				'user' => $user
			)
		);
	}
}
