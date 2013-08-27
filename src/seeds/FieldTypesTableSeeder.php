<?php

class LPressFieldTypesTableSeeder extends Seeder {

	public function run()
	{
		$field_types = array(
			array(
				'label' => 'wysiwyg',
				'description' => 'This field type is edited on the front-end interface using the wysiwyg engine.'
			),
			array(
				'label' => 'File',
				'description' => 'This field type is for a file input.'
			),
			array(
				'label' => 'Text',
				'description' => 'This field type is for a text input.'
			),
			array(
				'label' => 'Textarea',
				'description' => 'This field type is for a textarea.'
			),
			array(
				'label' => 'Email',
				'description' => 'This field type is for an email input.'
			),
			array(
				'label' => 'Phone',
				'description' => 'This field type is for a tel input.'
			),
			array(
				'label' => 'Date',
				'description' => 'This field type is for a date input.'
			),
			array(
				'label' => 'Time',
				'description' => 'This field type is for a time input.'
			),
			array(
				'label' => 'URL',
				'description' => 'This field type is for a url input.'
			),
			array(
				'label' => 'Color',
				'description' => 'This field type is for a color input.'
			),
			array(
				'label' => 'Password',
				'description' => 'This field type is for a password input.'
			),
			array(
				'label' => 'Select',
				'description' => 'This field type is for a single selection input.'
			),
			array(
				'label' => 'Multiselect',
				'description' => 'This field type is for a multiple selection input.'
			),
			array(
				'label' => 'Checkbox',
				'description' => 'This field type is for a true/false input.'
			)
		);

		// Uncomment the below to run the seeder
		DB::table('field_types')->insert($field_types);
	}

}
