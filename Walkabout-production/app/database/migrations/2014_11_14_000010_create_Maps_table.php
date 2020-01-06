<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMapsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('maps', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index();
			$table->string('hash',32);
			$table->string('name',30);
			$table->text('description');
			$table->string('logo',255)->nullable();
			$table->string('kml_layer',255)->nullable();
			$table->string('center',70);
			$table->tinyInteger('zoom');
			$table->integer('zoom_ctrl_pos')->nullable();
			$table->integer('maptype_ctrl_pos')->nullable();
			$table->integer('pan_ctrl_pos')->nullable();
			$table->integer('streetview_ctrl_pos')->nullable();
			$table->timestamps();
			$table->softDeletes();
			$table->engine = "InnoDB"; 
			$table->foreign('user_id')->references('id')->on('users');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('maps');
	}

}
