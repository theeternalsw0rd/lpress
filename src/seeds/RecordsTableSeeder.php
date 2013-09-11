<?php

class LPressRecordsTableSeeder extends Seeder {

	public function run() {
		$now = date('Y-m-d H:i:s');
		$records = array(
			array(
				'label' => 'Key',
				'slug' => 'key',
				'public' => TRUE,
				'author_id' => 1,
				'publisher_id' => 1,
				'record_type_id' => 5,
				'site_id' => 1,
				'created_at' => $now,
				'updated_at' => $now
			)
		);

		DB::table('records')->insert($records);
	}

}
