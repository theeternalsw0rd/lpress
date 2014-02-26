<?php namespace EternalSword\LPress;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class SiteController extends BaseController {
	public static function getSite($id) {
		extract(parent::prepareMake());	
		if(Auth::user()->isRoot()) {
			$site = Site::find($id);
			$themes = Theme::all();
			$theme_list = array();
			foreach($themes as $theme) {
				$theme_list[$theme->id] = $theme->label;
			}
			$pass_to_view = array(
				'view_prefix' => $view_prefix,
				'title' => 'Site: ' . $site->label,
				'site' => $site,
				'theme_list' => $theme_list
			);
			return View::make($view_prefix . '.dashboard.site', $pass_to_view);
		}
		return App::abort(403, 'Your account does not have sufficient privilege for the requested information.');
	}

	public static function postSite($id) {
	}
}
