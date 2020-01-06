<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserInteractionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
			Schema::create('interactions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('UID');
			$table->string('client_ip');
			$table->string('latitude');
			$table->string('longitude');
			$table->string('object');
			$table->string('object_id');
			$table->string('action');
			$table->dateTime('time');
			$table->engine = "InnoDB"; 
			
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		 Schema::drop('interactions');
	}

}
