<?php namespace EternalSword\LPress;

class Revision extends \Eloquent {

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
		return $this->belongsTo('\EternalSword\LPress\Value');
	}

	public function author() {
		return $this->belongsTo('\EternalSword\LPress\User', 'author_id');
	}

	public function publisher() {
		return $this->belongsTo('\EternalSword\LPress\User', 'publisher_id');
	}
}
