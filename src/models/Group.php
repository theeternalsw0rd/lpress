<?php namespace EternalSword\LPress;

class Group extends BaseModel { 
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
