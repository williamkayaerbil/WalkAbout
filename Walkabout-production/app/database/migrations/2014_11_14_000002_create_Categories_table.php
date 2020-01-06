<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('categories', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('group_id')->unsigned()->index();
			$table->integer('parent_id')->default(0);
			$table->string('code',3);
			$table->string('title',255);
			$table->string('tag',25);
			$table->text('description');
			$table->string('image',255)->nullable();
			$table->boolean('trusted');
			$table->tinyInteger('type');
			$table->softDeletes();
			$table->timestamps();
			$table->engine = "InnoDB"; 
			$table->foreign('group_id')->references('id')->on('groups');
			$table->unique(array('code', 'type'));
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('categories');
	}

}

