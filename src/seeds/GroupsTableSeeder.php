<?php

class LPressGroupsTableSeeder extends Seeder {

	public function run()
	{
		$date = new \DateTime;
		$groups = array(
			array(
				'label' => 'Root',
				'description' => 'This group has full control over all sites.',
				'site_id' => 0,
				'record_type_id' => 0,
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Manager',
				'description' => 'This group has full publishing and user management control over all sites.',
				'site_id' => 0,
				'record_type_id' => 0,
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Publisher',
				'description' => 'This group has full publishing control over all sites.',
				'site_id' => 0,
				'record_type_id' => 0,
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Editor',
				'description' => 'This group can create revisions of any records in all sites and can create, revise, and publish their own records.',
				'site_id' => 0,
				'record_type_id' => 0,
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Author',
				'description' => 'This group can create, revise, and publish their own records in all sites.',
				'site_id' => 0,
				'record_type_id' => 0,
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Contributor',
				'description' => 'This group can create new records and revisions of those records in all sites, but have no publishing permissions.',
				'site_id' => 0,
				'record_type_id' => 0,
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Subscriber',
				'description' => 'This group can create and edit their own comments (if enabled) and manage their user profile for all sites.',
				'site_id' => 0,
				'record_type_id' => -1,
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Wildcard Admin',
				'description' => 'This group has full control over the wildcard site.',
				'site_id' => 1,
				'record_type_id' => 0,
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Wildcard Manager',
				'description' => 'This group has publishing and user managment control over the wildcard site.',
				'site_id' => 1,
				'record_type_id' => 0,
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Wildcard Publisher',
				'description' => 'This group has full publishing control over the wildcard site.',
				'site_id' => 1,
				'record_type_id' => 0,
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Wildcard Editor',
				'description' => 'This group can create revisions of any records in the wildcard site and can create, revise, and publish their own records.',
				'site_id' => 1,
				'record_type_id' => 0,
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Wildcard Author',
				'description' => 'This group can create, revise, and publish their own records in the wildcard site.',
				'site_id' => 1,
				'record_type_id' => 0,
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Wildcard Contributor',
				'description' => 'This group can create new records and revisions of those records in the wildcard site, but have no publishing permissions.',
				'site_id' => 1,
				'record_type_id' => 0,
				'created_at' => $date,
				'updated_at' => $date
			),
			array(
				'label' => 'Wildcard Subscriber',
				'description' => 'This group can create and edit their own comments (if enabled) and manage their user profile for the wildcard site.',
				'site_id' => 1,
				'record_type_id' => -1,
				'created_at' => $date,
				'updated_at' => $date
			),
		);

		DB::table('groups')->insert($groups);
	}

}
