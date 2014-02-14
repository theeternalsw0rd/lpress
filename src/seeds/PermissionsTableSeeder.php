<?php

class LPressPermissionsTableSeeder extends Seeder {

	public function run()
	{
		$date = new \DateTime;
		$permissions = array(
			array(
				'label' => 'Root',
				'slug' => 'root',
				'description' => 'Can do anything.',
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'User Manager',
				'slug' => 'user-manager',
				'description' => 'Can update other user\'s details and remove user accounts.',
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Publish',
				'slug' => 'publish',
				'description' => 'Can make changes to front-end state for all records in scope.',
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Publish Own',
				'slug' => 'publish-own',
				'description' => 'Can make changes to front-end state for own records in scope.',
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Delete',
				'slug' => 'delete',
				'description' => 'Can delete any record or revision in scope.',
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Delete Own',
				'slug' => 'delete-own',
				'description' => 'Can delete own records or revisions in scope.',
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Edit',
				'slug' => 'edit',
				'description' => 'Can create revisions for all records in scope.',
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Create',
				'slug' => 'create',
				'description' => 'Can create new records and revisions for own records in scope.',
				'created_at' => $date,
				'updated_at' => $date
			)
		);

		DB::table('permissions')->insert($permissions);
	}

}
