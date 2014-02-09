<?php namespace EternalSword\LPress;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends \Eloquent implements UserInterface, RemindableInterface {

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
		'username' => 'required|unique:users',
		'email' => 'required|email|unique:users',
		'password' => 'required|min:8',
		'verify_password' => 'same:password',
		'email_visible' => 'bool',
		'user_image' => 'record_exists:avatars'
	);

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

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail() {
		return $this->email;
	}

	public function getRules() {
		return $this->rules;
	}

	public function hasPermission($permission) {
		$user = $this;
		$permission_array = is_string($permission) ? array($permission) : $permission;
		if(!isset($user->groups)) {
			$user->load('groups.permissions');
		}
		else {
			foreach($user->groups as &$group) {
				if(!isset($group->permissions)) {
					$group->load('permissions');
				}
			}
		}
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
}
