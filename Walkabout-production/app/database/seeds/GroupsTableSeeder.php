<?php



class GroupsTableSeeder extends Seeder {

	public function run()
	{
	

	
			Group::create([
				"name"=>"Atlanta maps",
				"description" =>"Atlanta Maps"
			]);


	
	}

}