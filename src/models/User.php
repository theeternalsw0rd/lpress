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
	protected $hidden = array('password', 'email', 'id');

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

	public function hasPermission($permission) {
		$user = $this;
		$permission_array = is_string($permission) ? array($permission) : $permission;
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
