<?php

class LPressGroupsTableSeeder extends Seeder {

	public function run()
	{
		$date = new \DateTime;
		$groups = array(
			array(
				'label' => 'Root',
				'description' => 'This group has all permissions within scope.',
				'record_type_id' => 0,
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Manager',
				'description' => 'This group has full publishing and user management control within scope.',
				'record_type_id' => 0,
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Publisher',
				'description' => 'This group has full publishing control within scope.',
				'record_type_id' => 0,
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Editor',
				'description' => 'This group can create revisions of any records within scope and can create, revise, and publish their own records.',
				'record_type_id' => 0,
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Author',
				'description' => 'This group can create, revise, and publish their own records within scope.',
				'record_type_id' => 0,
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Contributor',
				'description' => 'This group can create new records and revisions of those records within scope, but have no publishing permissions.',
				'record_type_id' => 0,
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Subscriber',
				'description' => 'This group can create and edit their own comments (if enabled) and manage their user profile within scope.',
				'record_type_id' => -1,
				'created_at' => $date,
				'updated_at' => $date
			)
		);

		DB::table('groups')->insert($groups);
	}

}
