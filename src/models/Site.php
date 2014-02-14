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

	protected static $columns = array(
		'id' => 'ID',
		'label' => 'Label',
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
