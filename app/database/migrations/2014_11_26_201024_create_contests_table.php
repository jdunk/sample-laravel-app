<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContestsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contests', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->integer('contest_region_id')->unsigned();
			$table->string('title', 255);
			$table->string('year', 4);
			$table->string('season', 10);
			$table->boolean('is_active')->default(false);
			$table->timestamps();
			$table->softDeletes();
			$table->unique(['contest_region_id', 'year', 'season']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('contests');
	}
}
