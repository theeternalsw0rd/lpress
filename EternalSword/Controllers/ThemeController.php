<?php namespace EternalSword\Controllers;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Request;

class ThemeController extends BaseController {
	public static function verifyTheme() {
		define('DOMAIN', Request::server('HTTP_HOST'));
		$site = NULL;
		try { 
			$site = Site::where('domain', DOMAIN)->first();
		} catch(\Exception $e) {
			$message = $e->getMessage();
			$code = $e->getCode();
			if($code == 2002) {
				echo Lang::get('l-press::errors.dbConnectionError');
				die;
			}
			if(substr_count($message, 'SQLSTATE[42S02]') > 0) {
				echo Lang::get('l-press::errors.dbTableMissing', array('table' => 'sites'));
				die;
			}
			echo Lang::get('l-press::errors.httpStatus500');
			die;
		}
		if(!$site) {
			$site = Site::where('domain', 'wildcard')->first();
		}
		if(!$site) {
			echo Lang::get('l-press::errors.siteMissing');
			die;
		}
		define('SITE', $site->id);
		define('PRODUCTION', $site->in_production == 1 ? 'compressed' : 'uncompressed');
		try {
			$theme = Theme::find($site->theme_id);
		} catch(\Exception $e) {
			$message = $e->getMessage();
			$code = $e->getCode();
			if(substr_count($message, 'SQLSTATE[42S02]') > 0) {
				echo Lang::get('l-press::errors.dbTableMissing', array('table' => 'themes'));
				die;
			}
			echo Lang::get('l-press::errors.httpStatus500');
			die;
		}
		define('THEME', $theme ? $theme->slug : 'default');
	}
}
