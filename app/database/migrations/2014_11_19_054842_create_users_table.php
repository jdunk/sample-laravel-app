<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('email')->nullable();
			$table->string('username', 32);
			$table->string('name', 100);
			$table->string('password', 100);
			$table->rememberToken();
			$table->string('image_small')->nullable();
			$table->string('image_large')->nullable();
			$table->tinyInteger('is_active')->unsigned()->default(0);
			$table->tinyInteger('is_confirmed')->unsigned()->default(0);
			$table->timestamps();
			$table->unique('email');
			$table->unique('username');
			$table->index('email');
			$table->index('username');
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
