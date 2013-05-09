<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLPressUsersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function(Blueprint $table) {
			$table->increments('id');
			$table->string('username')->unique();
            $table->string('email')->unique();
			$table->boolean('email_visible');
			$table->string('first_name');
			$table->string('last_name');
			$table->string('name_suffix');
			$table->string('name_prefix');
			$table->text('bio');
			$table->text('image');
			$table->string('password', 100);
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
        Schema::drop('users');
    }

}
