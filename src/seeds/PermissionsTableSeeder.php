<?php

class LPressPermissionsTableSeeder extends Seeder {

    public function run()
    {
		$permissions = array(
			array(
				'level' => 0,
				'name' => 'Site Admin',
				'description' => 'Users in this level have full access to administrate '
					. 'the associated site. Note that the Site Admin on the wildcard site '
					. 'is not equivalent to users set as root as the scope is limited to '
					. 'manage only the wildcard site and not any of the others.',
				'section' => 0
			),
			array(
				'level' => 1,
				'name' => 'Editor',
				'description' => 'Users in this level can create content and revisions of that content, and can publish any content.',
				'section' => 0
			),
			array(
				'level' => 2,
				'name' => 'Author',
				'description' => 'Users in this level can create content and revisions of that content, and can publish that content.',
				'section' => 0
			),
			array(
				'level' => 3,
				'name' => 'Contributor',
				'description' => 'Users in this level can create new content and revisions of that content, but cannot publish.',
				'section' => 0
			),
			array(
				'level' => 4,
				'name' => 'Subscriber',
				'description' => 'Users in this level have low level access, being able to have a profile on the site and make comments.',
				'section' => 0
			)
        );

        // Uncomment the below to run the seeder
		DB::table('lpress_permissions')->insert($permissions);
    }

}
