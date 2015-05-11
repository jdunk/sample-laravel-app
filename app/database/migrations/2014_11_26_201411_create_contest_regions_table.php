<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContestRegionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contest_regions', function(Blueprint $table)
		{
			$table->increments('id');

			// Like "Tokyo" or "US" or "Europe" or "Global"
			$table->string('title');

			// TO, US, EU, GL, possible hardcoded Strategy names
			$table->string('short_code');

			$table->timestamps();
			$table->softDeletes();

			$table->unique('short_code');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('contest_regions');
	}

}
