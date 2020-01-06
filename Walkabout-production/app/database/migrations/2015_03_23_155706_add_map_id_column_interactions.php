<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMapIdColumnInteractions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('interactions', function($table)
		{
		   $table->integer('map_id')->unsigned()->index(); 
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
		Schema::table('interactions', function($table)
		{  
			$table->dropColumn('map_id');
		});
	}

}
