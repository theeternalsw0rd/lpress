<?php

class LPressThemesTableSeeder extends Seeder {

	public function run()
	{
		$date = new \DateTime;
		$themes = array(
			array(
				'label' => 'Default Theme',
				'slug' => 'default',
				'description' => 'Default theme provided by LPress.',
				'created_at' => $date,
				'updated_at' => $date
			)
		);

		DB::table('themes')->insert($themes);
	}

}
