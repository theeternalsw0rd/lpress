<?php namespace EternalSword\LPress;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class Group extends \Eloquent { 
	public function permissions() {
		return $this->belongsToMany('\EternalSword\LPress\Permission');
	}

	public function users() {
		return $this->belongsToMany('\EternalSword\LPress\User');
	}

	public function site() {
		return $this->belongsTo('\EternalSword\LPress\Site');
	}
}
