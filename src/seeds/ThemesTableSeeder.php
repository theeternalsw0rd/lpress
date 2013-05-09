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

        // Uncomment the below to run the seeder
		DB::table('lpress_themes')->insert($themes);
    }

}
