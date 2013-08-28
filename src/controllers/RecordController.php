<?php namespace EternalSword\LPress;

use Illuminate\Routing\Controllers\Controller;
use Illuminate\Support\Facades\HTML;
use Illuminate\Support\Facades\Config;

class RecordController extends BaseController {
	public function getValuesByRecordType($slug) {
		$record_type = RecordType::where('slug', '=', $slug);
		$record_type->load('records.values');
		return $record_type->records->values;
	}

	public function getCurrentValue($value) {
		$value->load(
			array(
				'revisions' => function($query) {
					$query->where('id', '=', $value->current_revision_id);
				}
			)
		);
		return $value->revisions->first();
	}
}
