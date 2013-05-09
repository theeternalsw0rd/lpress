<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLPressCommentsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('lpress_comments', function(Blueprint $table) {
            $table->increments('id');
			$table->integer('record_id');
			$table->integer('author_id');
			$table->integer('parent_id');
			$table->integer('depth');
			$table->text('contents');
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
        Schema::drop('lpress_comments');
    }

}
