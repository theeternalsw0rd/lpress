<?php

class ThemesTableSeeder extends Seeder {

    public function run()
    {
        $themes = array(
			array(
				'name' => 'Default Theme',
				'slug' => 'default',
				'description' => 'Default theme provided by this application'
			)
        );

        // Uncomment the below to run the seeder
		DB::table('themes')->insert($themes);
    }

}
