<?php

class LPressMenuItemTypesTableSeeder extends Seeder {

    public function run()
    {
		$menu_item_types = array(
			array(
				'label' => 'Text',
			),
			array(
				'label' => 'Link',
			),
			array(
				'label' => 'Separator',
			)
        );

        // Uncomment the below to run the seeder
		DB::table('menu_item_types')->insert($menu_item_types);
    }

}
