<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUniqueCodeCategoriesConstraint extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('categories', function($table)
		{
		  	$table->dropUnique('categories_code_type_unique');
		  	$table->unique(array('code', 'type','group_id'));
		  	DB::statement('ALTER TABLE `categories` MODIFY `code` VARCHAR(3) NULL;');
		});
		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('categories', function($table)
		{
		  	$table->dropUnique('categories_code_type_group_id_unique');
		  	$table->unique(array('code', 'type'));
		  	DB::statement('ALTER TABLE `categories` MODIFY `code` VARCHAR(3);');
		});
	}

}
