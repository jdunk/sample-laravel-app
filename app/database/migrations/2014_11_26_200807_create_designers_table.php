<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDesignersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('designers', function(Blueprint $table)
		{
			/**
			 * Designer profiles live perpetually, so this model is home to any designer in the Acme system.
			 */
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			// designer profile title
			$table->string('title');
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
		Schema::drop('designers');
	}

}
