<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMentionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mentions', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');

			// Who mentioned it?
			$table->integer('user_id')->unsigned();

			// What was mentioned? User, Product, Post, Designer, Brand
			$table->string('subject_type', 50);
			$table->integer('subject_id')->unsigned();

			// Where was it mentioned?  Comment, Post
			$table->string('mentionable_type', 50);
			$table->integer('mentionable_id')->unsigned();

			$table->timestamps();

			$table->index('subject_type');
			$table->index('subject_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('mentions');
	}

}
