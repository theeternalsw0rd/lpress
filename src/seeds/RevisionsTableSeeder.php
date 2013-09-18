<?php

class LPressRevisionsTableSeeder extends Seeder {

	public function run() {
		$now = date('Y-m-d H:i:s');
		$revisions = array(
			array(
				'value_id' => 1,
				'author_id' => 1,
				'publisher_id' => 1,
				'prev_revision_id' => 0,
				'contents' => 'key.png',
				'created_at' => $now,
				'updated_at' => $now
			),
			array(
				'value_id' => 2,
				'author_id' => 1,
				'publisher_id' => 1,
				'prev_revision_id' => 0,
				'contents' => 'Key avatar preloaded for use at installation.',
				'created_at' => $now,
				'updated_at' => $now
			)
		);

		DB::table('revisions')->insert($revisions);
	}

}
