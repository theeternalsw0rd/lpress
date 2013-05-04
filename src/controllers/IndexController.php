<?php namespace EternalSword\LPress;

	use Illuminate\Routing\Controllers\Controller;
	use Illuminate\Support\Facades\View;
	use Illuminate\Support\Facades\Html;

	class IndexController extends BaseController {
		public function getIndex() {
			if(!defined('THEME')) {
				echo 'An unknown error occured, please try again later.';
				die();
			}
			parent::setupMacros();
			return View::make('l-press::themes.' . THEME . '.index', array('domain' => DOMAIN));
		}
	}
?>
