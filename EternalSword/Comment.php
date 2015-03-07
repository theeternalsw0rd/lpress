<?php namespace EternalSword;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class Comment extends BaseModel {

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
		return $this->belongsTo('\EternalSword\Record');
	}

	public function author() {
		return $this->belongsTo('\EternalSword\User', 'author_id');
	}

	public function publisher() {
		return $this->belongsTo('\EternalSword\User', 'publisher_id');
	}

	public function value() {
		return $this->morphOne('\EternalSword\Value', 'valuable');
	}
}
