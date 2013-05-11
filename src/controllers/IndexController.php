<?php namespace EternalSword\LPress;

	use Illuminate\Routing\Controllers\Controller;
	use Illuminate\Support\Facades\View;
	use Illuminate\Support\Facades\Html;
	use Illuminate\Support\Facades\Config;

	class IndexController extends BaseController {
		public function getIndex() {
			if(!defined('THEME')) {
				echo 'An unknown error occured, please try again later.';
				die();
			}
			$site = Site::where('domain', DOMAIN)->get()->toArray();
			if(empty($site)) {
				$site = Site::where('domain', 'wildcard')->get()->toArray();
			}
			$view_prefix = 'l-press::themes.' . THEME;
			return View::make($view_prefix . '.index', 
				array(
					'domain' => DOMAIN,
					'view_prefix' => $view_prefix,
					'title' => $site[0]['label'],
					'route_prefix' => Config::get('l-press::route_prefix')
				)
			);
		}
	}
?>
