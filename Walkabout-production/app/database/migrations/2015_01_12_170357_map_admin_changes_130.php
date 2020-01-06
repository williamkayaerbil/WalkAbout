<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MapAdminChanges130 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('maps', function($table)
		{
		   $table->string('header_logo',255);
		   $table->string('menu_logo',255);
		   $table->string('place_logo',255);
		   $table->string('event_logo',255);
		   $table->text('menu_options');
		   $table->dropColumn('logo');

		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
			$table->dropColumn('header_logo');
			$table->dropColumn('menu_logo');
			$table->dropColumn('place_logo');
			$table->dropColumn('event_logo');
			$table->dropColumn('menu_options');
			$table->string('logo',255)->nullable();
	}

}
