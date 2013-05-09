<?php namespace EternalSword\LPress;

class Site extends \Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'sites';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array();

	/**
	 * Get the users for this site.
	 *
	 * @return array of User objects 
	 */
	public function users()
	{
		return $this->belongsToMany('EternalSword\LPress\User')->withPivot('permission_id');
	}
}
