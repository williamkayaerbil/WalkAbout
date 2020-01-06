<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsUrlLogos extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		  Schema::table('maps', function($table)
		{
		   $table->string('header_logo_url',255);
		   $table->string('menu_logo_url',255);
		   $table->string('place_logo_url',255);
		   $table->string('event_logo_url',255);
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
			$table->dropColumn('header_logo_url');
			$table->dropColumn('menu_logo_url');
			$table->dropColumn('place_logo_url');
			$table->dropColumn('event_logo_url');
		});
	}

}
