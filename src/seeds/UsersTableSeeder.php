<?php
use Illuminate\Support\Facades\Config;

class LPressUsersTableSeeder extends Seeder {

	public function run()
	{
		$users = array(
			array(
				'email' => 'admin@lpress.local',
				'username' => 'lpress',
				'password' => Hash::make(Config::get('app.key'))
			)
		);

		DB::table('users')->insert($users);
	}

}
