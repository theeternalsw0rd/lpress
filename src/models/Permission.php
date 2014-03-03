<?php namespace EternalSword\LPress;

class Permission extends BaseModel {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'permissions';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array();

	public function groups() {
		return $this->belongsToMany('\EternalSword\LPress\Group');
	}
}
