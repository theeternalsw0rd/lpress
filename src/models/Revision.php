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
		return $this->belongsTo('Value');
	}

	public function previous_revision() {
		return $this->belongsTo('Revision', 'previous_revision_id');
	}

	public function next_revision() {
		return $this->hasOne('Revision', 'previous_revision_id');
	}

	public function author() {
		return $this->belongsTo('User', 'author_id');
	}

	public function publisher() {
		return $this->belongsTo('User', 'publisher_id');
	}
}
