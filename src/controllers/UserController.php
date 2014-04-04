<?php namespace EternalSword\LPress;

use Illuminate\Routing\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController {
	protected static function processForm($id = NULL) {
		if(is_null($id)) {
			$user = new User;
		}
		else {
			$user = User::find($id);
		}
		if(is_null($user)) {
			return Redirect::back()->withInput()->with(
				'errors',
				array(
					Lang::get(
						'l-press::errors.modelIdNotFound',
						array('id' => $id)
					)
				)
			);
		}
		$validator = Validator::make(Input::all(), $user->getRules(), CustomValidator::getOwnMessages());
		if($validator->fails()) {
			return Redirect::back()->withInput()->withErrors($validator);
		}
		$user->fill(Input::all());
		$user->password = Hash::make(Input::get('password'));
		$user->save();
		return Redirect::route('lpress-dashboard');
	}

	public static function updateUser($id) {
		if(self::hasPermission($id)) {
			return self::processForm($id);
		}
		return App::abort(403);
	}

	public static function hasPermission($id = NULL) {
		$user = Auth::user();
		if(is_null($id)) {
			return $user->hasPermission('user-manager');
		}
		return $user->id == $id || $user->hasPermission('user-manager');
	}
}
