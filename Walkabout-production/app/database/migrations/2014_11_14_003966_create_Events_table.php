<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('events', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('place_id')->unsigned()->index();
			$table->string('title',255);
			$table->text('description');
			$table->string('picture',50)->nullable();
			$table->dateTime('start');
			$table->dateTime('end');
			$table->string('locale',10);
			$table->tinyInteger('n_of_repeats');
			$table->integer('category_id')->unsigned()->index();
			$table->softDeletes();
			$table->timestamps();
			$table->engine = "InnoDB"; 
			$table->foreign('place_id')->references('id')->on('places');
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
		Schema::drop('events');
	}

}
