<?php namespace EternalSword\LPress;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class Comment extends \Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'comments';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array();

	public function record() {
		return $this->belongsTo('Record');
	}

	public function author() {
		return $this->belongsTo('User', 'author_id');
	}

	public function publisher() {
		return $this->belongsTo('User', 'publisher_id');
	}

	public function value() {
		return $this->morphOne('Value', 'valuable');
	}
}
