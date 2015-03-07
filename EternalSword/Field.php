<?php namespace EternalSword;

class Field extends BaseModel {

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
		return $this->hasMany('\EternalSword\Value');
	}

	public function field_types() {
		return $this->belongsTo('\EternalSword\FieldType');
	}

	public function record_types() {
		return $this->belongsTo('\EternalSword\RecordType');
	}
}
