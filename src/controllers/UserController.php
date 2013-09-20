<?php namespace EternalSword\LPress;

use Illuminate\Routing\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends BaseController {
	public static function updateUser() {
	}
	
	public static function installUser() {
	}

	public static function hasPermission($permission) {
		$permission_array = is_string($permission) ? array($permission) : $permission;
		$user = Auth::user();
		$user->load('groups.permissions');
		foreach($user->groups as $group) {
			if(($group->site_id === 0 || $group->site_id == SITE) /* site check (value of 0 is wildcard) */
			&& ($group->record_type_id === 0 || $group->record_type_id === $record_type->id) /* record type check (value of 0 is wildcard) */ ) {
				foreach($group->permissions as $permission) {
					if($permission->slug === 'root' || in_array($permission_array, $permission->slug)) {
						return TRUE;
					}
				}
			}
		}
		return FALSE;
	}
}
