<?php namespace EternalSword\LPress;

	use Illuminate\Routing\Controllers\Controller;
	use Illuminate\Support\Facades\View;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Route;
	use Illuminate\Support\Facades\Session;
	use Illuminate\Support\Facades\Redirect;

	class AuthenticationController extends Controller {
		public function getLogin() {
			if(!defined('THEME')) {
				echo 'An unknown error occured, please try again later.';
				die();
			}
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
			return View::make('l-press::themes.' . THEME . '.login',
				array(
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
			return View::make('l-press::themes.' . THEME . '.logged-in');
		}
	}
?>
