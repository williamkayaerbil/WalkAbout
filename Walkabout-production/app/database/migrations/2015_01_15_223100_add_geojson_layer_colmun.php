<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGeojsonLayerColmun extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		 Schema::table('maps', function($table)
		{
		   $table->string('geojson_layer',255)->nullable();
		   
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
			$table->dropColumn('geojson_layer');
		});
	}

}
