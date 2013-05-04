<?php

class LPressUsersTableSeeder extends Seeder {

    public function run()
    {
        $users = array(
			array(
				'email' => 'admin@lpress.local',
				'username' => 'admin',
				'password' => Hash::make('password'),
				'root' => true
			)
        );

        // Uncomment the below to run the seeder
		DB::table('lpress_users')->insert($users);
    }

}
