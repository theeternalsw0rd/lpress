<?php namespace EternalSword\LPress;

	use Illuminate\Routing\Controllers\Controller;
	use Illuminate\Support\Facades\View;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Route;
	use Illuminate\Support\Facades\Session;
	use Illuminate\Support\Facades\Redirect;

	class AuthenticationController extends Controller {
		public function getLogin($return_route) {
			if(!defined('THEME')) {
				echo 'An unknown error occured, please try again later.';
				die();
			}
			$user = Auth::user();
			if($user) {
				Session::put('return_route', $return_route);
				return Redirect::to('logout/logged');
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

		public function getLogout() {
			Auth::logout();
			Session::put('message', 'Successfully Logged Out');
			return Redirect::route('lpress-index');
		}

		public function getLogoutLogged() {
			$return_route = Session::get('return_route', 'lpress-index');
			return View::make('l-press::themes.' . THEME . '.logged-in', array('return_route', $return_route));
		}
	}
?>
