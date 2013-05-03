<?php namespace EternalSword\LPress;
	use Illuminate\Routing\Controllers\Controller;
	use Illuminate\Support\Facades\View;
	class IndexController extends Controller {
		public function getIndex() {
			if(!defined('THEME')) {
				echo 'An unknown error occured, please try again later.';
				die();
			}
			return View::make('l-press::themes.' . THEME . '.index', array('domain' => DOMAIN));
		}
	}
?>
