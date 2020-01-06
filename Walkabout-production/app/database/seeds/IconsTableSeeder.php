<?php



class IconsTableSeeder extends Seeder {

	public function run()
	{
		

		Icon::create(["name"=>"ic_atm.png"]);
		Icon::create(["name"=>"ic_concerts.png"]);
		Icon::create(["name"=>"ic_crafts.png"]);
		Icon::create(["name"=>"ic_hotel.png"]);
		Icon::create(["name"=>"ic_literary.png"]);
		Icon::create(["name"=>"ic_performance.png"]);
		Icon::create(["name"=>"ic_restaurant.png"]);
		Icon::create(["name"=>"ic_restrooms.png"]);
		Icon::create(["name"=>"ic_sights.png"]);
		Icon::create(["name"=>"ic_visualarts.png"]);
		Icon::create(["name"=>"events-exercise-icon.png"]);
		Icon::create(["name"=>"events-access-icon.png"]);
		Icon::create(["name"=>"events-health-icon.png"]);
		Icon::create(["name"=>"events-sponsor-icon.png"]);
		Icon::create(["name"=>"events-water-icon.png"]);
		Icon::create(["name"=>"events-ems-icon.png"]);
	}

}