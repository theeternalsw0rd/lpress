<?php namespace EternalSword;

class Record extends BaseModel {

	/**
		* The database table used by the model.
		*
		* @var string
		*/
	protected $table = 'records';

	/**
		* The attributes excluded from the model's JSON form.
		*
		* @var array
		*/
	protected $hidden = array();

	/**
		* The attributes which are guarded from new instances
		*
		* @var array
		*/
	protected $guarded = array('*');

	public function values() {
		return $this->morphMany('\EternalSword\Value', 'valuable');
	}

	public function comments() {
		return $this->hasMany('\EternalSword\Comment');
	}

	public function symlinks() {
		return $this->hasMany('\EternalSword\Symlink');
	}

	public function record_type() {
		return $this->belongsTo('\EternalSword\RecordType');
	}

	public function author() {
		return $this->belongsTo('\EternalSword\User', 'author_id');
	}

	public function publisher() {
		return $this->belongsTo('\EternalSword\User', 'publisher_id');
	}

	public function site() {
		return $this->belongsTo('\EternalSword\Site');
	}

	public function getPath($record_type, $path) {
		$current_record_type = $this->record_type()->first();
		if($current_record_type->id == $record_type->id) {
			return $path;
		}
		$path_ending = '';
		while($current_record_type->id != $record_type->id) {
			$path_ending = '/' . $current_record_type->slug;
			$current_record_type = $current_record_type->parent_type()->first();
		}
		return $path . $path_ending;
	}
}
