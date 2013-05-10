<?php

class LPressSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('LPressThemesTableSeeder');
		$this->call('LPressSitesTableSeeder');
		$this->call('LPressPermissionsTableSeeder');
		$this->call('LPressFieldTypesTableSeeder');
		$this->call('LPressFieldsTableSeeder');
		$this->call('LPressGroupsTableSeeder');
		$this->call('LPressGroupPermissionTableSeeder');
		$this->call('LPressRecordTypesTableSeeder');
		$this->call('LPressMenuItemTypesTableSeeder');
		$this->call('LPressUsersTableSeeder');
		$this->call('LPressGroupUserTableSeeder');
	}

}
