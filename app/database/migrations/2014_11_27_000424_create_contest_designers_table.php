<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContestDesignersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contest_designers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('contest_id')->unsigned();
			$table->integer('designer_id')->unsigned();
			$table->integer('display_order')->unsigned()->default(0);
			$table->integer('designer_vote_count_cache')->unsigned()->default(0);
			$table->timestamps();

			$table->index('designer_id');
			$table->index('contest_id');
			$table->index('display_order');
			$table->unique(['designer_id', 'contest_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('contest_designers');
	}

}
