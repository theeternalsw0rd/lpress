<?php
use Illuminate\Support\Facades\Config;

class LPressUsersTableSeeder extends Seeder {

	public function run()
	{
		$date = new \DateTime;
		$users = array(
			array(
				'email' => 'admin@lpress.local',
				'username' => 'lpress',
				'password' => Hash::make(Config::get('app.key')),
				'created_at' => $date,
				'updated_at' => $date
			)
		);

		DB::table('users')->insert($users);
	}

}
