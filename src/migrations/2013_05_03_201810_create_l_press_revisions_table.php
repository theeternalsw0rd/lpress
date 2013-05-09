<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLPressRevisionsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('revisions', function(Blueprint $table) {
            $table->increments('id');
			$table->integer('value_id');
			$table->integer('author_id');
			$table->integer('publisher_id');
			$table->integer('prev_revision_id');
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
        Schema::drop('revisions');
    }

}
