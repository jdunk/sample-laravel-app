<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMediaAssetDerivativesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('media_asset_derivatives', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('media_asset_id');
			// large, medium, small, original?
			$table->string('name', 50);
			// protocol drives strategy/behavior? s3:// etc.
			$table->string('uri', 255);
			$table->integer('width')->unsigned()->nullable();
			$table->integer('height')->unsigned()->nullable();
			$table->integer('filesize')->unsigned();
			$table->string('md5', 32)->nullable();
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
		Schema::drop('media_asset_derivatives');
	}
}
