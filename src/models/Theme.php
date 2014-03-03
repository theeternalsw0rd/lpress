<?php namespace EternalSword\LPress;

class Theme extends BaseModel {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'themes';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array();

	public function sites() {
		$this->hasMany('EternalSword\LPress\Site');
	}
}
