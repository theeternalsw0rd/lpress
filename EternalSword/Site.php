<?php namespace EternalSword;

class Site extends BaseModel {

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

	protected $fillable = array(
		'label',
		'domain',
		'theme_id',
		'in_production'
	);

	protected $rules = array(
		'label' => 'required',
		'domain' => 'required|domain|unique:sites,domain,:id:',
		'theme_id' => 'required|numeric',
		'in_production' => 'bool'
	);

	/**
	 * Get the users for this site.
	 *
	 * @return array of User objects 
	 */
	public function users() {
		return $this->hasMany('EternalSword\User', 'group_user');
	}

	public function groups() {
		return $this->hasMany('EternalSword\Group', 'group_user');
	}

	public function theme() {
		return $this->belongsTo('EternalSword\Theme');
	}
}	
