<?php namespace EternalSword;

class Revision extends BaseModel {

	/**
		* The database table used by the model.
		*
		* @var string
		*/
	protected $table = 'revisions';

	/**
		* The attributes excluded from the model's JSON form.
		*
		* @var array
		*/
	protected $hidden = array();

	public function value() {
		return $this->belongsTo('\EternalSword\Value');
	}

	public function author() {
		return $this->belongsTo('\EternalSword\User', 'author_id');
	}

	public function publisher() {
		return $this->belongsTo('\EternalSword\User', 'publisher_id');
	}
}
