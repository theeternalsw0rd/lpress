<?php namespace EternalSword\LPress;

class Field extends \Eloquent {

	/**
		* The database table used by the model.
		*
		* @var string
		*/
	protected $table = 'fields';

	/**
		* The attributes excluded from the model's JSON form.
		*
		* @var array
		*/
	protected $hidden = array();

	public function values() {
		return $this->hasMany('Value');
	}

	public function field_types() {
		return $this->belongsTo('FieldType');
	}

	public function record_types() {
		return $this->belongsTo('RecordType');
	}
}
