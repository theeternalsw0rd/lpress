<?php namespace EternalSword\Controllers;

use Illuminate\Support\Facades\Auth;

class SiteController extends BaseController {
	public static function hasPermission($id = NULL) {
		$user = Auth::user();
		if(is_null($id)) {
			return $user->isRoot();
		}
		return $user->hasPermission('root');
	}
}
