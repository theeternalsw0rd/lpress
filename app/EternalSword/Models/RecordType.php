<?php namespace App\EternalSword\Models;

class RecordType extends BaseModel {
	/**
		* The database table used by the model.
		*
		* @var string
		*/
	protected $table = 'record_types';

	/**
		* The attributes excluded from the model's JSON form.
		*
		* @var array
		*/
	protected $hidden = array();

	public function parent_type() {
		return $this->belongsTo('\App\EternalSword\Models\RecordType', 'parent_id');
	}

	public function children() {
		return $this->hasMany('\App\EternalSword\Models\RecordType', 'parent_id');
	}

	public function records() {
		return $this->hasMany('\App\EternalSword\Models\Record');
	}

	public function symlinks() {
		return $this->hasMany('\App\EternalSword\Models\Symlink');
	}

	public function fields() {
		return $this->hasMany('\App\EternalSword\Models\Field');
	}

}

