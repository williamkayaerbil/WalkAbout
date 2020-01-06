<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function($table)
		{
		    $table->increments('id');
		    $table->integer('role');
		    $table->string('name');
		    $table->string('login');
		    $table->string('password');
		    $table->string('email')->unique();
		    $table->integer('group_id')->unsigned()->index();
		    $table->softDeletes();
		    $table->timestamps();
		    $table->rememberToken();
		    $table->engine = "InnoDB"; 
		    $table->foreign('group_id')->references('id')->on('groups');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('users');
	}

}
