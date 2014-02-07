<?php namespace EternalSword\LPress;

use Illuminate\Routing\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
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
				array('Could not find user by id provided implicitly. It\'s possible the user was deleted by another user or the posted data was corrupt.')
			);
		}
		$validator = Validator::make(Input::all(), $user->getRules(), CustomValidator::getOwnMessages());
		if($validator->passes()) {
			Redirect::back()->withInput()->with('messages', Input::all());
		}
		return Redirect::back()->withInput()->withErrors($validator);
	}

	public static function updateUser() {
		$auth_user = Auth::user();
		$id = Input::get('user_id');
		if($auth_user->id == $id || $auth_user->hasPermission('user-manager')) {
			return self::processForm($id);
		}
		return App::abort(403);
	}
}
