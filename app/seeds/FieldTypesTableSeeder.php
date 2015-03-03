<?php

class LPressFieldTypesTableSeeder extends Seeder {

	public function run()
	{
		$date = new \DateTime;
		$field_types = array(
			array(
				'label' => 'wysiwyg',
				'description' => 'This field type is edited on the front-end interface using the wysiwyg engine.',
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'File',
				'description' => 'This field type is for a file input.',
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Text',
				'description' => 'This field type is for a text input.',
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Textarea',
				'description' => 'This field type is for a textarea.',
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Email',
				'description' => 'This field type is for an email input.',
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Phone',
				'description' => 'This field type is for a tel input.',
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Date',
				'description' => 'This field type is for a date input.',
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Time',
				'description' => 'This field type is for a time input.',
				'created_at' => $date,
				'updated_at' => $date

			),
			array(
				'label' => 'URL',
				'description' => 'This field type is for a url input.',
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Color',
				'description' => 'This field type is for a color input.',
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Password',
				'description' => 'This field type is for a password input.',
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Select',
				'description' => 'This field type is for a single selection input.',
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Multiselect',
				'description' => 'This field type is for a multiple selection input.',
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Checkbox',
				'description' => 'This field type is for a true/false input.',
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Commentbox',
				'description' => 'This field type is for comments tied to records',
				'created_at' => $date,
				'updated_at' => $date
			)
		);

		DB::table('field_types')->insert($field_types);
	}

}
