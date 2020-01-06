<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePlacesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('places', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('layer_id')->unsigned()->index();
			$table->string('title',255);
			$table->text('description');
			$table->string('picture',50)->nullable();
			$table->string('location',255);
			$table->double('lat');
			$table->double('lng');
			$table->dateTime('start');
			$table->dateTime('end');
			$table->string('website',255)->nullable();
			$table->boolean('verified');
			$table->integer('category_id')->unsigned()->index();
			$table->softDeletes();
			$table->timestamps();
			$table->engine = "InnoDB"; 
			$table->foreign('layer_id')->references('id')->on('layers');
			$table->foreign('category_id')->references('id')->on('categories');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('places');
	}

}
