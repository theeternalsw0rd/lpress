<?php

class LPressRevisionsTableSeeder extends Seeder {

	public function run() {
		$date = new \DateTime;
		$revisions = array(
			array(
				'value_id' => 1,
				'author_id' => 1,
				'publisher_id' => 1,
				'prev_revision_id' => 0,
				'contents' => 'key.png',
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'value_id' => 2,
				'author_id' => 1,
				'publisher_id' => 1,
				'prev_revision_id' => 0,
				'contents' => 'Key avatar preloaded for use at installation.',
				'created_at' => $date,
				'updated_at' => $date
			)
		);

		DB::table('revisions')->insert($revisions);
	}

}
