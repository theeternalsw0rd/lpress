<?php

class LPressValuesTableSeeder extends Seeder {

	public function run() {
		$date = new \DateTime;
		$values = array(
			array(
				'field_id' => 4,
				'valuable_id' => 1,
				'valuable_type' => 'EternalSword\\LPress\\Record',
				'current_revision_id' => 1,
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'field_id' => 5,
				'valuable_id' => 1,
				'valuable_type' => 'EternalSword\\LPress\\Record',
				'current_revision_id' => 2,
				'created_at' => $date,
				'updated_at' => $date
			)
		);

		DB::table('values')->insert($values);
	}

}
