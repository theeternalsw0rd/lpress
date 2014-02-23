<?php namespace EternalSword\LPress;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class DashboardController extends BaseController {
	public static function getDashboard() {
		extract(parent::prepareMake());	
		$user = Auth::user();
		$is_root = $user->isRoot();
		$pass_to_view = array(
			'view_prefix' => $view_prefix,
			'title' => 'Dashboard',
			'user' => $user,
			'is_root' => $is_root
		);
		if($is_root) {
			$sites = Site::take(5)->get();
			$pass_to_view['sites'] = $sites->load('theme');
			$pass_to_view['columns'] = Site::getColumns();
		}
		return View::make($view_prefix . '.dashboard.index', $pass_to_view);
	}
}
