<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSocialColumns extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		 Schema::table('places', function($table)
		{
		   $table->string('facebook',255);
		   $table->string('twitter',255); 
		   $table->string('instagram',255);
		   
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{

		  Schema::table('places', function($table)
		{
			$table->dropColumn('facebook');
			$table->dropColumn('twitter');
			$table->dropColumn('instagram');
		});
	}

}
