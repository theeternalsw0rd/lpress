<?php

class LPressRecordTypesTableSeeder extends Seeder {

	public function run() {
		$record_types = array(
			array(
				'label' => 'Post',
				'label_plural' => 'Posts',
				'description' => 'Default type for content.',
				'slug' => 'posts',
				'parent_id' => 0,
				'depth' => 0
			),
			array(
				'label' => 'Link',
				'label_plural' => 'Links',
				'description' => 'Link to external resources easily by name.',
				'slug' => 'links',
				'parent_id' => 0,
				'depth' => 0
			),
			array(
				'label' => 'Attachment',
				'label_plural' => 'Attachments',
				'description' => 'Default type for uploaded files.',
				'slug' => 'attachments',
				'parent_id' => 0,
				'depth' => 0
			),
			array(
				'label' => 'Image',
				'label_plural' => 'Images',
				'description' => 'Attachments that are images.',
				'slug' => 'images',
				'parent_id' => 3,
				'depth' => 1
			),
			array(
				'label' => 'Avatar',
				'label_plural' => 'Avatars',
				'description' => 'Images that are avatars.',
				'slug' => 'avatars',
				'parent_id' => 4,
				'depth' => 2
			)
		);

		DB::table('record_types')->insert($record_types);
	}

}
