<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		
		Schema::create('files', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
		    $table->increments('id');
		    $table->string('name');
		    $table->string('url');
		    $table->integer('user_id')->unsigned()->index()->nullable();
		    $table->integer('group_id')->unsigned()->index()->nullable(); 
		    $table->timestamps();
		    $table->engine = "InnoDB"; 

	        //$table->foreign('user_id')->references('id')->on('users');
		    //$table->foreign('group_id')->references('id')->on('groups');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
			Schema::drop('files');
	}

}
