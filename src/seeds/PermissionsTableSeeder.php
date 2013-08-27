<?php

class LPressPermissionsTableSeeder extends Seeder {

	public function run()
	{
		$permissions = array(
			array(
				'label' => 'Root',
				'description' => 'Can do anything.'
			),
			array(
				'label' => 'Publish',
				'description' => 'Make changes to front-end state for all records in scope.'
			),
			array(
				'label' => 'Publish Own',
				'description' => 'Make changes to front-end state for own records in scope.'
			),
			array(
				'label' => 'Delete',
				'description' => 'Can delete any record or revision in scope.'
			),
			array(
				'label' => 'Delete Own',
				'description' => 'Can delete own records or revisions in scope.'
			),
			array(
				'label' => 'Edit',
				'description' => 'Can create revisions for all records in scope.'
			),
			array(
				'label' => 'Create',
				'description' => 'Can create new records and revisions for own records in scope.'
			)
		);

		DB::table('permissions')->insert($permissions);
	}

}
