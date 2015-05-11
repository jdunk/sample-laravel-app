<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInstagramAccountsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('instagram_accounts', function(Blueprint $table)
		{
			$table->increments('id');

			$table->integer('user_id');

			// probably bigInt but using string because it's not our system
			$table->string('uid', 30)->nullable();

			$table->string('nickname')->nullable();
			$table->string('name')->nullable();
			$table->text('description')->nullable();
			$table->string('image_url')->nullable();

			$table->string('access_token')->nullable();

			$table->timestamps();

			$table->index('uid');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('instagram_accounts');
	}

}
