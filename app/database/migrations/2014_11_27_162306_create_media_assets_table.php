<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMediaAssetsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('media_assets', function(Blueprint $table)
		{
			$table->increments('id');
			// Designer, Judge, JudgeContestant, Product, etc
			$table->string('media_assetable_type', 50);
			$table->integer('media_assetable_id')->unsigned();
			$table->integer('user_id')->unsigned();
			// Image, Video, YouTube, Vimeo; strategy selector
			$table->string('type', 50);
			// UploadedFileName.png
			$table->string('title', 100);
			// Eureka
			$table->string('caption', 255);
			$table->integer('display_order')->default(0);
			// Location (maybe this can drive mongo cache?)
			$table->decimal('lat', 10, 8)->nullable();
			$table->decimal('lng', 11, 8)->nullable();
			//$table->string('uri_lg'); // see media_asset_variants
			//$table->string('uri_sm')->nullable();
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
		Schema::drop('media_assets');
	}

}
