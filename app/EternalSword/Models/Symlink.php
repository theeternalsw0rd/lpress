<?php namespace App\EternalSword\Models;

class Symlink extends BaseModel {

	/**
		* The database table used by the model.
		*
		* @var string
		*/
	protected $table = 'symlinks';

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
		return $this->belongsTo('\App\EternalSword\Models\Record');
	}

	public function record_type() {
		return $this->belongsTo('\App\EternalSword\Models\RecordType');
	}

	public function site() {
		return $this->belongsTo('\App\EternalSword\Models\Site');
	}
}
