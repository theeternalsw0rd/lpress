<?php namespace EternalSword\LPress;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;

class User extends BaseModel implements UserInterface, RemindableInterface {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array(
		'password',
		'email',
		'id'
	);

	protected $guarded = array(
		'id',
		'password'
	);

	protected $fillable = array(
		'username',
		'email',
		'email_visible',
		'first_name',
		'last_name',
		'name_suffix',
		'name_prefix',
		'bio',
		'image'
	);

	protected $rules = array(
		'username' => 'required|unique:users,username,:id:',
		'email' => 'required|email|unique:users,email,:id:',
		'password' => 'required|min:8',
		'verify_password' => 'same:password',
		'email_visible' => 'bool',
		'user_image' => 'record_exists:avatars'
	);

	protected $special_inputs = array(
		'image' => 'attachment:avatars',
		'bio' => 'text:textarea',
		'email' => 'text:email'
	);

	protected function hasModelPermission($action) {
		$user = Auth::user();
		return $user->hasPermission('user-manager');
	}

	public function deleteItem() {
		if(!$this->hasModelPermission('delete')) {
			return Redirect::back()->with(
				'std_errors',
				array(
					Lang::get('l-press::errors.executePermissionsError')
				)
			);
		}
		if($this->id == Auth::user()->id) {
			return Redirect::back()->with(
				'std_errors',
				array(
					Lang::get('l-press::errors.deleteCurrentUser')
				)
			);
		}
		if(self::all()->count() == 1) {
			return Redirect::back()->with(
				'std_errors',
				array(
					Lang::get('l-press::errors.lastModelItem')
				)
			);
		}
		$this->delete();
	}

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier() {
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword() {
		return $this->password;
	}

	public function getRememberToken() {
		return $this->remember_token;
	}

	public function setRememberToken($value) {
		$this->remember_token = $value;
	}

	public function getRememberTokenName() {
		return 'remember_token';
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail() {
		return $this->email;
	}

	public function getLabelAttribute() {
		$name_suffix = $this->name_suffix;
		$name_suffix = empty($name_suffix) ? '' : ', ' . $name_suffix;
		return $this->name_prefix . ' ' . $this->first_name . ' ' . $this->last_name . $name_suffix;
	}

	public function hasPermission($permission) {
		$user = $this->loadPermissions();
		$permission_array = is_string($permission) ? array($permission) : $permission;
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

	public function isRoot() {
		$user = $this->loadPermissions();
		foreach($user->groups as $group) {
			if($group->site_id === 0) {
				foreach($group->permissions as $permission) {
					if($permission->slug === 'root') {
						return TRUE;
					}
				}
			}
		}
		return FALSE;
	}

	public function published_records() {
		return $this->hasMany('\EternalSword\LPress\Record', 'publisher_id');
	}

	public function authored_records() {
		return $this->hasMany('\EternalSword\LPress\Record', 'author_id');
	}

	public function published_revisions() {
		return $this->hasMany('\EternalSword\LPress\Revision', 'publisher_id');
	}

	public function authored_revisions() {
		return $this->hasMany('\EternalSword\LPress\Revision', 'author_id');
	}

	public function groups() {
		return $this->belongsToMany('\EternalSword\LPress\Group');
	}

	private function loadPermissions() {
		if(!isset($this->groups)) {
			$this->load('groups.permissions');
		}
		else {
			foreach($this->groups as &$group) {
				if(!isset($group->permissions)) {
					$group->load('permissions');
				}
			}
		}
		return $this;
	}
}
