<?php namespace EternalSword;

class Group extends BaseModel { 
	protected $table = 'groups';

	protected $fillable = array(
		'label',
		'description',
		'record_type_id'
	);

	protected $rules = array(
		'label' => 'required|unique:groups,label,:id:',
		'description' => 'required'
	);

	protected $pivots = array(
		'users' => 'fa-user',
		'permissions' => 'fa-key'
	);
	
	public function permissions() {
		return $this->belongsToMany('\EternalSword\Permission');
	}

	public function users() {
		return $this->belongsToMany('\EternalSword\User');
	}

	public function site() {
		return $this->belongsToMany('\EternalSword\Site', 'group_user');
	}

	public function getSiteIdAttribute() {
		return (int)$this->pivot->site_id;
	}
}
