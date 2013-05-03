<?php namespace EternalSword\LPress;

class Site extends \Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'lpress_sites';

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
	/*public function users()
	{
		return $this->belongsToMany('EternalSword\LPress\User')->withPivot('permission_id');
	}

	/**
	 * Get the theme for this site.
	 *
	 * @return Theme object
	 */
	public function getTheme()
	{
		return Theme::find($this->theme_id);
	}
}
