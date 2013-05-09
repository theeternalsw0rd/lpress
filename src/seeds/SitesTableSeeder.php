<?php

class LPressSitesTableSeeder extends Seeder {

    public function run()
    {
        $sites = array(
			array(
				'label' => 'Wildcard',
				'domain' => 'wildcard',
				'theme_id' => 1
			)
        );

        // Uncomment the below to run the seeder
		DB::table('lpress_sites')->insert($sites);
    }

}
