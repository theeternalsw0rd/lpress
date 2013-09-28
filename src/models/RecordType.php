<?php namespace EternalSword\LPress;

class RecordType extends \Eloquent {
	public $filtered_records;

	/**
		* The database table used by the model.
		*
		* @var string
		*/
	protected $table = 'record_types';

	/**
		* The attributes excluded from the model's JSON form.
		*
		* @var array
		*/
	protected $hidden = array();

	protected $appends = array('filtered_records');

	public function getFilteredRecordsAttribute() {
		if(is_object($this->filtered_records)) {
			return $this->attributes['filtered_records'] = $this->filtered_records->toArray();
		}
		return $this->attributes['filtered_records'] = array();
	}

	public function parent_type() {
		return $this->belongsTo('\EternalSword\LPress\RecordType', 'parent_id');
	}

	public function children() {
		return $this->hasMany('\EternalSword\LPress\RecordType', 'parent_id');
	}

	public function filtered_records($public = TRUE) {
		$records = Record::where('site_id', '=', SITE)
			->where('record_type_id', '=', $this->id)
			->where('public', '=', $public)->get();
		$records->load(
			'author',
			'publisher',
			'values.field',
			'values.current_revision.author',
			'values.current_revision.publisher'
		);
		if(Site::find(SITE)->domain != 'wildcard') {
			$wildcard = Site::where('domain', '=', 'wildcard')->first();
			if(count($wildcard) > 0) {
				$wildcard_records = Record::where('site_id', '=', $wildcard->id)
					->where('record_type_id', '=', $this->id)
					->where('public', '=', $public);
				if(count($records) > 0) {
					$record_slugs = $records->lists('slug');
					$wildcard_records = $wildcard_records->whereNotIn('slug', $record_slugs)->get();
				}
				else {
					$wildcard_records = $wildcard_records->get();
				}
				$wildcard_records->load(
					'author',
					'publisher',
					'values.field',
					'values.current_revision.author',
					'values.current_revision.publisher'
				);
				$records = $records->merge($wildcard_records)->sortBy(function($record) {
					return $record->updated_at;
				})->reverse();
			}
		}
		$this->filtered_records = $records;
	}

	public function records() {
		return $this->hasMany('\EternalSword\LPress\Record');
	}

	public function aliases() {
		return $this->hasMany('\EternalSword\LPress\Record', 'alias_id');
	}

	public function fields() {
		return $this->hasMany('\EternalSword\LPress\Field');
	}
}

