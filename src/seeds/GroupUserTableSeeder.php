<?php

class LPressGroupUserTableSeeder extends Seeder {

	public function run()
	{
		$date = new \DateTime;
		$group_user = array(
			array(
				'group_id' => '1',
				'user_id' => '1',
				'site_id' => '0',
				'created_at' => $date,
				'updated_at' => $date
			)
		);

		DB::table('group_user')->insert($group_user);
	}

}
