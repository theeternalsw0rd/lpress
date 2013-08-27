<?php

class LPressThemesTableSeeder extends Seeder {

	public function run()
	{
		$themes = array(
			array(
				'label' => 'Default Theme',
				'slug' => 'default',
				'description' => 'Default theme provided by LPress.'
			)
		);

		DB::table('themes')->insert($themes);
	}

}
