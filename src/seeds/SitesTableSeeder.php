<?php

class SitesTableSeeder extends Seeder {

    public function run()
    {
        $sites = array(
			array(
				'theme_id' => 1,
				'title' => 'My Awesome Site',
				'domain' => '*'
			)
        );

        // Uncomment the below to run the seeder
		DB::table('sites')->insert($sites);
    }

}
