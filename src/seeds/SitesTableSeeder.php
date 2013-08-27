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

		DB::table('sites')->insert($sites);
	}

}
