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

	/**
		* The attributes which are guarded from new instances
		*
		* @var array
		*/
	protected $guarded = array('*');

	public function values() {
		return $this->morphMany('\EternalSword\LPress\Value', 'valuable');
	}

	public function comments() {
		return $this->hasMany('\EternalSword\LPress\Comment');
	}

	public function aliases() {
		return $this->hasMany('\EternalSword\LPress\Record', 'alias_id');
	}

	public function record_type() {
		return $this->belongsTo('\EternalSword\LPress\RecordType');
	}

	public function author() {
		return $this->belongsTo('\EternalSword\LPress\User', 'author_id');
	}

	public function publisher() {
		return $this->belongsTo('\EternalSword\LPress\User', 'publisher_id');
	}

	public function site() {
		return $this->belongsTo('\EternalSword\LPress\Site');
	}
}
