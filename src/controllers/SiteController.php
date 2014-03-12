<?php namespace EternalSword\LPress;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class SiteController extends BaseController {
	public static function createSite() {
	}

	public static function updateSite($id) {
	}

	public static function getSites() {
		extract(parent::prepareMake());
		$sites = Site::paginate(15);
		$pass_to_view = array(
			'view_prefix' => $view_prefix,
			'title' => 'Site Management',
			'sites' => $sites,
			'new_site' => new Site
		);
		return View::make($view_prefix . '.dashboard.sites.index', $pass_to_view);
	}
}
