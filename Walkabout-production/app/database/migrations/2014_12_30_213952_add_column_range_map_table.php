<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnRangeMapTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
			Schema::table('maps', function($table)
		{
		    $table->time('start_time')->default('08:00:00');
		    $table->time('final_time')->default('22:00:00');
		   	$table->integer('step')->default(60); //step in minutes
		   	
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('maps', function($table)
		{
    		$table->dropColumn('start_time');
    		$table->dropColumn('final_time');
    		$table->dropColumn('step'); 
		});
	}

}
