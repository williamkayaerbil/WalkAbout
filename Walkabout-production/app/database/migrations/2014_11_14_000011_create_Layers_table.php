<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLayersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('layers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name',30);
			$table->text('description');
			$table->integer('map_id')->unsigned()->index();
			$table->timestamps();
			$table->softDeletes();
			$table->engine = "InnoDB"; 
			$table->foreign('map_id')->references('id')->on('maps');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('layers');
	}

}
