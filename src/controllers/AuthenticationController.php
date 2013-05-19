<?php namespace EternalSword\LPress;

	use Illuminate\Routing\Controllers\Controller;
	use Illuminate\Support\Facades\View;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Route;
	use Illuminate\Support\Facades\Session;
	use Illuminate\Support\Facades\Redirect;
	use Illuminate\Support\Facades\HTML;
	use Illuminate\Support\Facades\Config;

	class AuthenticationController extends BaseController {
		public function getLogin() {
			if(!defined('THEME')) {
				echo 'An unknown error occured, please try again later.';
				die();
			}
			parent::setMacros();
			$view_prefix = 'l-press::themes.' . THEME;
			$first_user = User::find(1);
			if($first_user->username == 'lpress') {
				echo 'We will write the installer here';
				die();
			}
			$user = Auth::user();
			if($user) {
				return Redirect::route('lpress-logout-logged');
			}
			$login_failed = Session::get('bad_login', false);
			Session::forget('bad_login');
			return View::make($view_prefix . '.login',
				array(
					'login_failed' => $login_failed,
					'view_prefix' => $view_prefix,
					'title' => 'Login'
				)
			);
		}

		public function getLogout() {
			Auth::logout();
			Session::put('message', 'Successfully Logged Out');
			return Redirect::route('lpress-index');
		}

		public function getLogoutLogged() {
			parent::setMacros();
			$view_prefix = 'l-press::themes.' . THEME;
			return View::make($view_prefix . '.logged-in',
				array(
					'view_prefix' => $view_prefix,
					'title' => 'Already logged in'
				)
			);
		}
	}
?>
