<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAccountColumnsUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		 Schema::table('users', function($table)
		{
		   $table->integer('quota')->unsigned();
		   $table->date('expiration_date')->nullable();;
		  
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function($table)
		{  
			$table->dropColumn('quota');
			$table->dropColumn('expiration_date');
		});
	}

}
