<?php

class LPressGroupPermissionTableSeeder extends Seeder {

	public function run()
	{
		$group_permissions = array(
			array(
				'group_id' => 1,
				'permission_id' => 1
			),
			array(
				'group_id' => 2,
				'permission_id' => 2
			),
			array(
				'group_id' => 2,
				'permission_id' => 3
			),
			array(
				'group_id' => 2,
				'permission_id' => 4
			),
			array(
				'group_id' => 2,
				'permission_id' => 5
			),
			array(
				'group_id' => 2,
				'permission_id' => 6
			),
			array(
				'group_id' => 2,
				'permission_id' => 7
			),
			array(
				'group_id' => 3,
				'permission_id' => 3
			),
			array(
				'group_id' => 3,
				'permission_id' => 5
			),
			array(
				'group_id' => 3,
				'permission_id' => 6
			),
			array(
				'group_id' => 3,
				'permission_id' => 7
			),
			array(
				'group_id' => 4,
				'permission_id' => 3
			),
			array(
				'group_id' => 4,
				'permission_id' => 5
			),
			array(
				'group_id' => 4,
				'permission_id' => 7
			),
			array(
				'group_id' => 5,
				'permission_id' => 7
			),
			array(
				'group_id' => 7,
				'permission_id' => 1
			),
			array(
				'group_id' => 8,
				'permission_id' => 2
			),
			array(
				'group_id' => 8,
				'permission_id' => 3
			),
			array(
				'group_id' => 8,
				'permission_id' => 4
			),
			array(
				'group_id' => 8,
				'permission_id' => 5
			),
			array(
				'group_id' => 8,
				'permission_id' => 6
			),
			array(
				'group_id' => 8,
				'permission_id' => 7
			),
			array(
				'group_id' => 9,
				'permission_id' => 3
			),
			array(
				'group_id' => 9,
				'permission_id' => 5
			),
			array(
				'group_id' => 9,
				'permission_id' => 6
			),
			array(
				'group_id' => 9,
				'permission_id' => 7
			),
			array(
				'group_id' => 10,
				'permission_id' => 3
			),
			array(
				'group_id' => 10,
				'permission_id' => 5
			),
			array(
				'group_id' => 10,
				'permission_id' => 7
			),
			array(
				'group_id' => 11,
				'permission_id' => 7
			)
		);

		DB::table('group_permission')->insert($group_permissions);
	}

}
