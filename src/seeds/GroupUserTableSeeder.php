<?php

class LPressGroupUserTableSeeder extends Seeder {

	public function run()
	{
		$group_user = array(
			array(
				'group_id' => '1',
				'user_id' => '1'
			)
		);

		DB::table('group_user')->insert($group_user);
	}

}
