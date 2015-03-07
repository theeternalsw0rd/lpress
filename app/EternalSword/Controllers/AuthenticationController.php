<?php namespace App\EternalSword\Controllers;

use Illuminate\Routing\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\HTML;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use GrahamCampbell\HTMLMin\Facades\HTMLMin;
use Illuminate\Support\Facades\View;

class AuthenticationController extends BaseController {
	public function getLogin() {
		extract(parent::prepareMake());	
		$first_user = User::find(1);
		$login_failed = Session::get('bad_login', false);
		Session::forget('bad_login');
		$user = Auth::user();
		$title = Lang::get('l-press::titles.login');
		$install = FALSE;
		if($first_user->username == 'lpress') {
			if($user) Auth::logout();
			$user = FALSE;
			$title = Lang::get('l-press::titles.installer');
			$install = TRUE;
		}
		if($user) {
			return Redirect::route('lpress-logout-logged');
		}
		return HTMLMin::live(View::make($view_prefix . '.authentication.login',
			array(
				'login_failed' => $login_failed,
				'view_prefix' => $view_prefix,
				'title' => $title,
				'install' => $install
			)
		));
	}

	public function getLogout() {
		Auth::logout();
		Session::flush();
		Session::put('message', 'Successfully Logged Out');
		return Redirect::route('lpress-index');
	}

	public function getLogoutLogged() {
		extract(parent::prepareMake());	
		return HTMLMin::live(View::make($view_prefix . '.authentication.logged-in',
			array(
				'view_prefix' => $view_prefix,
				'title' => Lang::get('l-press::titles.loggedIn')
			)
		));
	}

	public function verifyLogin() {
		$remember = Input::get('remember') || FALSE;
		if(Auth::attempt(
			array(
				'username' => Input::get('username'),
				'password' => Input::get('password')
			),
			$remember
		)) {
			if(Input::get('username') == 'lpress') {
				return Redirect::route('lpress-dashboard.installer');
			}
			$route_prefix = Config::get('lpress::settings.route_prefix');
			$redirect = Session::get('redirect', $route_prefix);
			Session::forget('redirect');
			return Redirect::to($redirect);
		}
		Session::put('bad_login', true);
		return Redirect::route('lpress-login');
	}
}
