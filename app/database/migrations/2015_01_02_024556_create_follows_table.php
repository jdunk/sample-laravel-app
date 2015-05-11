<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFollowsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('follows', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->string('followable_type', 100);
			$table->integer('followable_id')->unsigned();
			$table->boolean('is_hushed')->default(false);
			$table->timestamps();
			$table->unique(['user_id', 'followable_id', 'followable_type']);
			$table->index('followable_type');
			$table->index('followable_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('follows');
	}

}
