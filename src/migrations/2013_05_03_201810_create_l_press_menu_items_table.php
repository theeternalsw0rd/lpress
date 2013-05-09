<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLPressMenuItemsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lpress_menu_items', function(Blueprint $table) {
			$table->increments('id');
			$table->string('label');
			$table->integer('menu_id');
			$table->integer('order');
			$table->integer('menu_item_type_id');
			$table->integer('record_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('lpress_menu_items');
    }

}
