<?php namespace EternalSword\LPress;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class Group extends \Eloquent { 
	public function permissions() {
		return $this->belongsToMany('Permission');
	}

	public function users() {
		return $this->belongsToMany('User');
	}

	public function site() {
		return $this->belongsTo('Site');
	}
}
