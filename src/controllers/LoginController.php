<?php namespace EternalSword\LPress;

	use Illuminate\Routing\Controllers\Controller;
	use Illuminate\Support\Facades\View;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Route;
	use Illuminate\Support\Facades\Session;
	use Illuminate\Support\Facades\Redirect;

	class LoginController extends Controller {
		public function getLogin($return_route) {
			if(!defined('THEME')) {
				echo 'An unknown error occured, please try again later.';
				die();
			}
			$user = Auth::user();
			if($user) {
				Redirect::to('logout/' . $return_route);
			}
			$login_failed = Session::get('bad_login', false);
			Session::forget('bad_login');
			return View::make('l-press::themes.' . THEME . '.login',
				array(
					'return_route' => $return_route,
					'login_failed' => $login_failed
				)
			);
		}
	}
?>
