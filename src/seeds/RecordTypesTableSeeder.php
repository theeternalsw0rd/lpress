<?php

class LPressRecordTypesTableSeeder extends Seeder {

    public function run()
    {
        $record_types = array(
			array(
				'label' => 'Post',
				'description' => 'Default type for content.',
				'slug' => 'post',
				'hide_slug' => TRUE,
				'parent_id' => 0,
				'depth' => 0
			),
			array(
				'label' => 'Link',
				'description' => 'Link to external resources easily by name.',
				'slug' => 'link',
				'hide_slug' => TRUE,
				'parent_id' => 0,
				'depth' => 0

			),
			array(
				'label' => 'Attachment',
				'description' => 'Default type for uploaded files.',
				'slug' => 'attachment',
				'hide_slug' => TRUE,
				'parent_id' => 0,
				'depth' => 0

			),
        );

        // Uncomment the below to run the seeder
		DB::table('lpress_record_types')->insert($record_types);
    }

}
