<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJudgeContestantsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('judge_contestants', function(Blueprint $table)
		{
			/**
			 * Users who want to be judges, this will be for internal viewing through the
			 * back-end only for now.  Entry into contest will be in contest_judge_contestants
			 */
			$table->increments('id');
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
		Schema::drop('judge_contestants');
	}

}
