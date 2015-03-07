<?php

class LPressSitesTableSeeder extends Seeder {

	public function run()
	{
		$date = new \DateTime;
		$sites = array(
			array(
				'label' => 'Wildcard',
				'domain' => 'wildcard',
				'theme_id' => 1,
				'created_at' => $date,
				'updated_at' => $date
			)
		);

		DB::table('sites')->insert($sites);
	}

}
