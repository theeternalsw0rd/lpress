<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLPressRecordTypesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('record_types', function(Blueprint $table) {
            $table->increments('id');
            $table->string('label')->unique();
            $table->string('slug')->unique();
            $table->boolean('hide_slug');
            $table->integer('parent_id');
            $table->integer('depth');
            $table->text('description');
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
        Schema::drop('record_types');
    }

}
