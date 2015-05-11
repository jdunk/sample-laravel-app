<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStringsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('strings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title');
			$table->string('name', 100);
			$table->text('content');
			$table->timestamps();

			$table->index('name');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('strings');
	}

}
