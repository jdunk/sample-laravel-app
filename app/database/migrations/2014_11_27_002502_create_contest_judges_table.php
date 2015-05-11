<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContestJudgesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contest_judges', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('display_order')->unsigned()->default(0);
			$table->integer('contest_id')->unsigned();
			$table->integer('judge_id')->unsigned();
			$table->timestamps();

			$table->unique(['contest_id', 'judge_id']);
			$table->index('display_order');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('contest_judges');
	}

}
