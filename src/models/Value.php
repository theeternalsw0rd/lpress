<?php namespace EternalSword\LPress;

class Value extends \Eloquent {

	/**
		* The database table used by the model.
		*
		* @var string
		*/
	protected $table = 'values';

	/**
		* The attributes excluded from the model's JSON form.
		*
		* @var array
		*/
	protected $hidden = array();

	public function field() {
		return $this->belongsTo('\EternalSword\LPress\Field');
	}

	public function valuable() {
		return $this->morphTo();
	}

	public function current_revision() {
		return $this->belongsTo('\EternalSword\LPress\Revision', 'current_revision_id');
	}

	public function revisions() {
		return $this->hasMany('\EternalSword\LPress\Revision');
	}
}
