<?php namespace EternalSword\Controllers;

use Illuminate\Support\Facades\Auth;
use EternalSword\Models\BaseModel;
use EternalSword\Models\Comment;
use EternalSword\Models\Field;
use EternalSword\Models\FieldType;
use EternalSword\Models\Group;
use EternalSword\Models\Permission;
use EternalSword\Models\Record;
use EternalSword\Models\RecordType;
use EternalSword\Models\Revision;
use EternalSword\Models\Site;
use EternalSword\Models\Symlink;
use EternalSword\Models\Theme;
use EternalSword\Models\User;
use EternalSword\Models\Value;

class SiteController extends BaseController {
	public static function hasPermission($id = NULL) {
		$user = Auth::user();
		if(is_null($id)) {
			return $user->isRoot();
		}
		return $user->hasPermission('root');
	}
}
