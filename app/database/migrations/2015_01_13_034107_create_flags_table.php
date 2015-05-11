<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFlagsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('flags', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->string('flaggable_type', 100);
			$table->integer('flaggable_id')->unsigned();
			$table->timestamps();
			$table->unique(['user_id', 'flaggable_id', 'flaggable_type']);
			$table->index('flaggable_type');
			$table->index('flaggable_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('flags');
	}
}
