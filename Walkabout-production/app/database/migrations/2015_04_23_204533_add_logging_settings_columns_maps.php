<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLoggingSettingsColumnsMaps extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('maps', function($table)
		{
		   $table->boolean('click_event')->default(false); 
		   $table->boolean('navigation_event')->default(false); 
		   $table->integer('navigation_time')->default(30); 
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
			$table->dropColumn('click_event');
			$table->dropColumn('navigation_event');
			$table->dropColumn('navigation_time');
		});
	}

}
