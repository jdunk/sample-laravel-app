<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFlaggedItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('flagged_items', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('flaggable_type', 100);
			$table->integer('flaggable_id')->unsigned();
			$table->integer('flag_count')->unsigned()->default(1);
			$table->integer('offending_user_id')->unsigned();
			$table->tinyInteger('severity')->unsigned()->nullable();
			$table->string('action', 255)->nullable();
			$table->integer('action_user_id')->unsigned()->nullable();
			$table->dateTime('action_created_at')->nullable();
			$table->timestamps();
			$table->unique(['flaggable_id', 'flaggable_type']);
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
		Schema::drop('flagged_items');
	}
}
