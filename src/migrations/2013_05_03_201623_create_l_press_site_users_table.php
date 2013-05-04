<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLPressSiteUsersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('lpress_site_user', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('site_id');
			$table->integer('user_id');
			$table->integer('permission_id');
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
		Schema::drop('lpress_site_user');
    }

}
