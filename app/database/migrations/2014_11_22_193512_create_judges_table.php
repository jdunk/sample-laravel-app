<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJudgesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('judges', function(Blueprint $table)
		{
			/**
			 * Judges are living entities.  A judge can participate in multiple Contests.  See contest_judges.
			 */
			$table->increments('id');
			// indicate that this is a winning judge contestant
			$table->boolean('is_judge_contestant')->default(false);
			$table->integer('user_id')->unsigned();
			$table->text('bio')->nullable();
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
		Schema::drop('judges');
	}

}
