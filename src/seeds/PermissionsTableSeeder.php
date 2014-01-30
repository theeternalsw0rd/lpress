<?php

class LPressPermissionsTableSeeder extends Seeder {

	public function run()
	{
		$permissions = array(
			array(
				'label' => 'Root',
				'slug' => 'root',
				'description' => 'Can do anything.'
			),
			array(
				'label' => 'User Manager',
				'slug' => 'user-manager',
				'description' => 'Can update other user\'s details and remove user accounts.'
			),
			array(
				'label' => 'Publish',
				'slug' => 'publish',
				'description' => 'Make changes to front-end state for all records in scope.'
			),
			array(
				'label' => 'Publish Own',
				'slug' => 'publish-own',
				'description' => 'Make changes to front-end state for own records in scope.'
			),
			array(
				'label' => 'Delete',
				'slug' => 'delete',
				'description' => 'Can delete any record or revision in scope.'
			),
			array(
				'label' => 'Delete Own',
				'slug' => 'delete-own',
				'description' => 'Can delete own records or revisions in scope.'
			),
			array(
				'label' => 'Edit',
				'slug' => 'edit',
				'description' => 'Can create revisions for all records in scope.'
			),
			array(
				'label' => 'Create',
				'slug' => 'create',
				'description' => 'Can create new records and revisions for own records in scope.'
			)
		);

		DB::table('permissions')->insert($permissions);
	}

}
