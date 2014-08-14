<?php namespace EternalSword\LPress;

use Illuminate\Routing\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController {
	public static function hasPermission($id = NULL) {
		$user = Auth::user();
		if(is_null($id)) {
			return $user->hasPermission('user-manager');
		}
		return $user->id == $id || $user->hasPermission('user-manager');
	}

	public static function getRedirect($id = NULL) {
		$route_prefix = (new PrefixGenerator)->getPrefix();
		$dashboard_route = '+' . Config::get('l-press::dashboard_route');
		$prefix = $route_prefix . '/' . $dashboard_route;
		if(is_null($id)) {
			return $prefix . '/users/:id:/groups';
		}
		return $prefix;
	}
}
