<?php namespace EternalSword;

class Value extends BaseModel {

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
		return $this->belongsTo('\EternalSword\Field');
	}

	public function valuable() {
		return $this->morphTo();
	}

	public function current_revision() {
		return $this->belongsTo('\EternalSword\Revision', 'current_revision_id');
	}

	public function revisions() {
	return $this->hasMany('\EternalSword\Revision');
	}
}
