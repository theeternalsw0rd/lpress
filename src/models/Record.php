<?php namespace EternalSword\LPress;

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
		return $this->morphMany('\EternalSword\LPress\Value', 'valuable');
	}

	public function comments() {
		return $this->hasMany('\EternalSword\LPress\Comment');
	}

	public function symlinks() {
		return $this->hasMany('\EternalSword\LPress\Symlink');
	}

	public function record_type() {
		return $this->belongsTo('\EternalSword\LPress\RecordType');
	}

	public function author() {
		return $this->belongsTo('\EternalSword\LPress\User', 'author_id');
	}

	public function publisher() {
		return $this->belongsTo('\EternalSword\LPress\User', 'publisher_id');
	}

	public function site() {
		return $this->belongsTo('\EternalSword\LPress\Site');
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
