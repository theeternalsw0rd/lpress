<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLPressRecordsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('records', function(Blueprint $table) {
            $table->increments('id');
			$table->string('label');
			$table->string('slug');
			$table->boolean('public');
			$table->integer('parent_id');
			$table->integer('creator_id');
			$table->integer('publisher_id');
			$table->integer('record_type_id');
			$table->dateTime('checked_out_time');
			$table->dateTime('checked_in_time');
			$table->integer('site_id');
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
        Schema::drop('records');
    }

}
