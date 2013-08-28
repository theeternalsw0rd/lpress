<?php namespace EternalSword\LPress;

class Record extends \Eloquent {

	/**
		* The database table used by the model.
		*
		* @var string
		*/
	protected $table = 'records';

	/**
		* The attributes excluded from the model's JSON form.
		*
		* @var array
		*/
	protected $hidden = array();

	public function values() {
		return $this->morphMany('Value', 'valuable');
	}

	public function comments() {
		return $this->hasMany('Comment');
	}

	public function record_type() {
		return $this->belongsTo('RecordType');
	}

	public function author() {
		return $this->belongsTo('User', 'author_id');
	}

	public function publisher() {
		return $this->belongsTo('User', 'publisher_id');
	}

	public function site() {
		return $this->belongsTo('Site');
	}
}
