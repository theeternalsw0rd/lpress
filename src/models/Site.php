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

	protected $fillable = array(
		'label',
		'domain',
		'theme_id',
		'in_production'
	);

	protected $rules = array(
		'label' => 'required',
		'domain' => 'required|domain|unique:sites',
		'theme_id' => 'required|numeric',
		'in_production' => 'bool'
	);

	protected static $columns = array(
		'id' => 'ID',
		'domain' => 'Domain',
		'theme->label' => 'Theme',
		'in_production' => 'In Production',
		'created_at' => 'Created At',
		'updated_at' => 'Updated At'
	);

	/**
	 * Get the users for this site.
	 *
	 * @return array of User objects 
	 */
	public function users() {
		return $this->belongsToMany('EternalSword\LPress\User')->withPivot('permission_id');
	}

	public function theme() {
		return $this->belongsTo('EternalSword\LPress\Theme');
	}

	public static function getColumns() {
		return self::$columns;
	}
}
