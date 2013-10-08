<?php namespace EternalSword\LPress;

class Alias extends \Eloquent {

	/**
		* The database table used by the model.
		*
		* @var string
		*/
	protected $table = 'aliases';

	/**
		* The attributes excluded from the model's JSON form.
		*
		* @var array
		*/
	protected $hidden = array();

	/**
		* The attributes which are guarded from new instances
		*
		* @var array
		*/
	protected $guarded = array('*');

	public function record() {
		return $this->belongsTo('\EternalSword\LPress\Record');
	}

	public function record_type() {
		return $this->belongsTo('\EternalSword\LPress\RecordType');
	}

	public function site() {
		return $this->belongsTo('\EternalSword\LPress\Site');
	}
}
