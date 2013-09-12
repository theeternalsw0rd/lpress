<?php

class LPressValuesTableSeeder extends Seeder {

	public function run() {
		$now = date('Y-m-d H:i:s');
		$values = array(
			array(
				'field_id' => 4,
				'valuable_id' => 1,
				'valuable_type' => 'EternalSword\\LPress\\Record',
				'current_revision_id' => 1,
				'description' => 'Key avatar preloaded for use at installation.',
				'created_at' => $now,
				'updated_at' => $now
			)
		);

		DB::table('values')->insert($values);
	}

}
