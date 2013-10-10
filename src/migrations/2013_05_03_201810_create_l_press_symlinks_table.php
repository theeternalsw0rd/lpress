<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLPressSymlinksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('symlinks', function(Blueprint $table) {
			$table->increments('id');
			$table->string('label');
			$table->string('slug');
			$table->integer('record_id');
			$table->integer('record_type_id');
			$table->integer('site_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('symlinks');
	}

}
