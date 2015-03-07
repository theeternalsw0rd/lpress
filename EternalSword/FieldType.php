<?php namespace EternalSword;

class FieldType extends BaseModel {

	/**
		* The database table used by the model.
		*
		* @var string
		*/
	protected $table = 'field_types';

	/**
		* The attributes excluded from the model's JSON form.
		*
		* @var array
		*/
	protected $hidden = array();

	public function fields() {
		return $this->hasMany('\EternalSword\Field');
	}
}

