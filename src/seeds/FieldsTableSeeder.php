<?php

class LPressFieldsTableSeeder extends Seeder {

	public function run() {
		$fields = array(
			array(
				'label' => 'Post Content',
				'slug' => 'post-content',
				'description' => 'This is the wysiwig for the Post record type.',
				'required' => TRUE,
				'field_type_id' => 1,
				'record_type_id' => 1
			),
			array(
				'label' => 'Comment Content',
				'slug' => 'comment-content',
				'description' => 'This is the editor for Comments.',
				'required' => TRUE,
				'field_type_id' => 15,
				'record_type_id' => -1
			),
			array(
				'label' => 'URL',
				'slug' => 'url',
				'description' => 'This is the url field for the Link record type.',
				'required' => TRUE,
				'field_type_id' => 8,
				'record_type_id' => 2
			),
			array(
				'label' => 'File',
				'slug' => 'file',
				'description' => 'This is the file input for the Attachment record type.',
				'required' => TRUE,
				'field_type_id' => 2,
				'record_type_id' => 3
			),
			array(
				'label' => 'File Description',
				'slug' => 'file-description',
				'description' => 'This is the textbox for description/caption for associated Attachment',
				'required' => FALSE,
				'field_type_id' => 3,
				'record_type_id' => 3
			)
		);

		DB::table('fields')->insert($fields);
	}

}
