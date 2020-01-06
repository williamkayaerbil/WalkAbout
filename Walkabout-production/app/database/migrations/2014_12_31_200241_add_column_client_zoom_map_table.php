<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnClientZoomMapTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
			Schema::table('maps', function($table)
		{
		    $table->integer('client_zoom')->default('18');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{	Schema::table('maps', function($table)
		{
			$table->dropColumn('client_zoom');
		});
	}

}
