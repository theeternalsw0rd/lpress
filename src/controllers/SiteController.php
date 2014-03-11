<?php namespace EternalSword\LPress;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class SiteController extends BaseController {
	public static function getForm($id = NULL) {
		extract(parent::prepareMake());
		if(is_null($id)) {
			$site = new Site;
			$title = 'Create New Site';
		}
		else {
			$site = Site::find($id);
			$title = 'Update Site: ' . $site->label;
		}
		$pass_to_view = array(
			'view_prefix' => $view_prefix,
			'title' => $title,
			'site' => $site
		);
		return View::make($view_prefix . '.dashboard.sites.form', $pass_to_view);
	}

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
