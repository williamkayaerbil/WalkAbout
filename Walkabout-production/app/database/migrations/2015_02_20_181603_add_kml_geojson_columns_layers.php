<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKmlGeojsonColumnsLayers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
			Schema::table('layers', function($table)
		{
		   $table->string('kml_layer',255)->nullable();
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
			Schema::table('layers', function($table)
		{  
			$table->dropColumn('kml_layer');
			$table->dropColumn('geojson_layer');
		});
	}
	

}
