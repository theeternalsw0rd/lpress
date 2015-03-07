<?php

class LPressRecordsTableSeeder extends Seeder {

	public function run() {
		$date = new \DateTime;
		$records = array(
			array(
				'label' => 'Key',
				'slug' => 'key',
				'public' => true,
				'author_id' => 1,
				'publisher_id' => 1,
				'record_type_id' => 5,
				'site_id' => 1,
				'created_at' => $date,
				'updated_at' => $date
			)
		);

		DB::table('records')->insert($records);
	}
}
