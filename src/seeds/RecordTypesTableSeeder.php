<?php

class LPressRecordTypesTableSeeder extends Seeder {

	public function run()
	{
		$record_types = array(
			array(
				'label' => 'Post',
				'description' => 'Default type for content.',
				'slug' => 'post',
				'parent_id' => 0,
				'depth' => 0
			),
			array(
				'label' => 'Link',
				'description' => 'Link to external resources easily by name.',
				'slug' => 'link',
				'parent_id' => 0,
				'depth' => 0
			),
			array(
				'label' => 'Attachment',
				'description' => 'Default type for uploaded files.',
				'slug' => 'attachment',
				'parent_id' => 0,
				'depth' => 0
			),
			array(
				'label' => 'Images',
				'description' => 'Attachments that are images.',
				'slug' => 'image',
				'parent_id' => 3,
				'depth' => 1
			)
		);

		DB::table('record_types')->insert($record_types);
	}

}
