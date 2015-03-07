<?php

class LPressFieldsTableSeeder extends Seeder {

	public function run() {
		$date = new \DateTime;
		$fields = array(
			array(
				'label' => 'Post Content',
				'slug' => 'post-content',
				'description' => 'This is the wysiwig for the Post record type.',
				'required' => true,
				'field_type_id' => 1,
				'record_type_id' => 1,
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Comment Content',
				'slug' => 'comment-content',
				'description' => 'This is the editor for Comments.',
				'required' => true,
				'field_type_id' => 15,
				'record_type_id' => -1,
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'URL',
				'slug' => 'url',
				'description' => 'This is the url field for the Link record type.',
				'required' => true,
				'field_type_id' => 8,
				'record_type_id' => 2,
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'File',
				'slug' => 'file',
				'description' => 'This is the file input for the Attachment record type.',
				'required' => true,
				'field_type_id' => 2,
				'record_type_id' => 3,
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'File Description',
				'slug' => 'file-description',
				'description' => 'This is the textbox for description/caption for associated Attachment',
				'required' => false,
				'field_type_id' => 3,
				'record_type_id' => 3,
				'created_at' => $date,
				'updated_at' => $date
			)
		);

		DB::table('fields')->insert($fields);
	}

}
