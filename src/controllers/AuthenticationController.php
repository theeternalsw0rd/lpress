<?php namespace EternalSword\LPress;

use Illuminate\Routing\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\HTML;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class AuthenticationController extends BaseController {
	public function getLogin() {
		if(!defined('THEME')) {
			echo 'An unknown error occured, please try again later.';
			die();
		}
		extract(parent::prepareMake());	
		$first_user = User::find(1);
		$login_failed = Session::get('bad_login', false);
		Session::forget('bad_login');
		$user = Auth::user();
		$title = 'Login';
		$install = FALSE;
		if($first_user->username == 'lpress') {
			if($user) Auth::logout();
			$user = FALSE;
			$title = 'LPress Installer';
			$install = TRUE;
		}
		if($user) {
			return Redirect::route('lpress-logout-logged');
		}
		return View::make($view_prefix . '.authentication.login',
			array(
				'login_failed' => $login_failed,
				'view_prefix' => $view_prefix,
				'title' => $title,
				'install' => $install
			)
		);
	}

	public function getLogout() {
		Auth::logout();
		Session::put('message', 'Successfully Logged Out');
		return Redirect::route('lpress-index');
	}

	public function getLogoutLogged() {
		extract(parent::prepareMake());	
		return View::make($view_prefix . '.authentication.logged-in',
			array(
				'view_prefix' => $view_prefix,
				'title' => 'Already logged in'
			)
		);
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
			$route_prefix = Config::get('l-press::route_prefix');
			$redirect = Session::get('redirect', $route_prefix);
			Session::forget('redirect');
			return Redirect::to($redirect);
		}
		Session::put('bad_login', true);
		return Redirect::route('lpress-login');
	}
}
