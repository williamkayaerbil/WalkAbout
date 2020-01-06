<?php


Route::get('/', function()
{
	return Redirect::to('/admin');
});
Route::post('/register',function(){
	return Input::all();
});
Route::get('/login/logon',function(){
	return Redirect::to('/login');
});
Route::get('/login','LoginController@index');


Route::post('/login/logon', 'LoginController@logon');
Route::get('/login/logout', function(){
	Auth::logout();
	return Redirect::to('/login');
});
Route::get('/map/{hash}','PublicMapsController@index');
Route::get('/api/data/{hash}','ApiController@data');
Route::get('/api/get-icon','ApiController@getIcon');
Route::post('/api/interactions','ApiController@saveInteractions');

Route::controller('password','RemindersController');
Route::group(array('before' => 'auth'), function()
{
	Route::get('/admin',function(){
		return View::make("admin.home");
	});
	Route::get('admin/expiration','SettingsController@expiration');
	Route::get('admin/info','SettingsController@info');
	Route::get('/admin/categories/export','CategoriesController@export');
	Route::resource('/admin/categories','CategoriesController');
	Route::post('/admin/categories/delete','CategoriesController@delete');
	Route::get('/admin/categories/{id}/restore','CategoriesController@restore');
	Route::post('/admin/categories/import','CategoriesController@import');
	Route::get('/admin/events/export','EventsController@export');
	Route::resource('/admin/events','EventsController');
	Route::post('/admin/events/delete','EventsController@delete');
	Route::get('/admin/events/{id}/restore','EventsController@restore');
	Route::post('/admin/events/import','EventsController@import');
	Route::get('/admin/places/export','PlacesController@export');
	Route::post('/admin/places/upload-photos','PlacesController@upload_photos');
	Route::post('/admin/places/save-uploaded-photos','PlacesController@save_uploaded_photos');
	Route::get('/admin/places/other-places','PlacesController@other_places');

	Route::resource('/admin/places','PlacesController');
	Route::post('/admin/places/delete','PlacesController@delete');
	Route::get('/admin/places/{id}/restore','PlacesController@restore');
	Route::post('/admin/places/import','PlacesController@import');
	Route::get('/admin/maps/export','MapsController@export');
	Route::resource('/admin/maps','MapsController');
	Route::post('/admin/maps/delete','MapsController@delete');
	Route::get('/admin/maps/{id}/restore','MapsController@restore');
	Route::post('/admin/maps/import','MapsController@import');
	Route::get('/admin/layers/export','LayersController@export');
	Route::resource('/admin/layers','LayersController');
	Route::post('/admin/layers/delete','LayersController@delete');
	Route::get('/admin/layers/{id}/restore','LayersController@restore');
	Route::post('/admin/layers/import','LayersController@import');
	Route::get('/admin/users/export','UsersController@export');
	Route::resource('/admin/users','UsersController');
	Route::post('/admin/users/delete','UsersController@delete');
	Route::get('/admin/users/{id}/restore','UsersController@restore');
	Route::post('/admin/users/import','UsersController@import');
	Route::get('/admin/groups/export','GroupsController@export');
	Route::resource('/admin/groups','GroupsController');
	Route::post('/admin/groups/import','GroupsController@import');
	Route::post('/admin/groups/delete','GroupsController@delete');
	Route::get('/admin/groups/{id}/restore','GroupsController@restore');
	Route::post('/admin/categories/import','CategoriesController@import');
	Route::get('/api/layers','ApiController@layers');
	Route::get('/api/places','ApiController@places');
	Route::get('/api/users','ApiController@users');
	Route::get('/api/categories','ApiController@categories');

	Route::resource('/admin/settings','SettingsController');
	
	Route::get('/admin/reports','ReportsController@index');
	
	Route::get('/media/dialog','MediaController@dialog');
	Route::post('/media/execute','MediaController@execute');
	Route::post('/media/force_download','MediaController@force_download');
	Route::post('/media/upload','MediaController@upload');


});

Route::get('icons','ApiController@getIcons');

Route::get('add-user',function(){
	/*$u=new User();
	$u->email="admin@gmail.com";
	$u->password="123";
	$u->save();
	return "ok";*/
});
Route::get('deploy',function(){
	date_default_timezone_set('America/Chicago');
	$deploy = new Deploy('/var/www/vhosts/MyCityWalkabout.com/dev.MyCityWalkabout.com');
	$deploy->execute();
});

Route::get('migration',function(){

	$c=DB::connection('migration');
	
	$users=$c->table('users')->select('users.*')->get();
	//$categories=$c->table('categories')->select('categories.*')->get();
	

	//foreach ($categories as $key => $category) {
	//	$image=explode(',',$category->image);
		Category::create(array(
			'id'=>1,
			'code'=>'tem',
			'title'=>'temp',
			'tag'=>'temp',
			'description'=>'Temporal',
			'trusted'=>'false',
			'type'=>1, //place
			'image'=>'',
			'group_id'=>1,
		));
	//}
	$admin=Role::find(1);


	foreach ($users as $key => $user) {
		$newUser=User::create(array(
			'group_id'=>1,
			'email'=>$user->login,
			'password'=>"12345",
			'name'=>$user->name
		));



		$newUser->attachRole( $admin );

		$maps=$c->table('maps')->where('owner_id','=',$user->id)->select('maps.*')->get();
		
		foreach ($maps as $key => $map) {
			
			$logo=explode("/",$map->logo);
			$kml_layer=explode("/",$map->kml_layer);
			$map->user_id=$newUser->id;
			$map->logo=$logo[count($logo)-1];
			$map->kml_layer=$kml_layer[count($kml_layer)-1];
			$newMap=Map::create((array)$map);
			$newMap->hash=$map->hash;
			$newMap->save();

			$layers=$c->table('layers')->leftJoin('map_layers','layer_id','=','layers.id')
			->where('map_id','=',$map->id)->get();


			
			foreach ($layers as $key => $layer) {
				$newLayer= Layer::create(
					array(
						"name"=>$layer->name,
						"description"=>$layer->description,
						'map_id'=>$newMap->id
					)
				);

				$places=$c->table('places')->where('layer_id','=',$layer->id)->get();
				foreach ($places as $key => $place) {
					$newPlace=Place::create(
						array(
							'layer_id'=>$newLayer->id,
							'title'=>$place->title,
							'picture'=>$place->picture,
							'description'=>$place->description,
							'lat'=>$place->lat,
							'lng'=>$place->lng,
							'website'=>$place->website,
							'verified'=>$place->verified,
							'category_id'=>1,
							'location'=>$place->location,
							'verified'=>$place->verified,
							'start'=>$place->start,
							'end'=>$place->end,
						)
					);
					$events=$c->table('events')->leftJoin('event_place','event_id','=','events.id')->where('place_id','=',$place->id)->get();
					
					foreach ($events as $key => $event) {
						Event::create(array(
							'title'=>$event->title,
							'description'=>$event->description,
							'picture'=>$event->picture,
							'start'=>$event->start,
							'end'=>$event->end,
							'locale'=>$event->locale,
							'n_of_repeats'=>$event->n_of_repeats,
							'verified'=>$event->verified,
							'place_id'=>$newPlace->id,
							'category_id'=>1,
						));
					}
				}
			}

		}

		}
		//Update categories places
		$places=$c->table('places')->leftJoin('incident_category','places.id','=','incident_id')
							->leftJoin('categories','categories.id','=','incident_category.category_id')
							->select(DB::raw('places.*,categories.code as code, categories.title as cat_title,
						 	         categories.tag as cat_tag, categories.description as cat_description,
						 	         categories.trusted as cat_trusted,categories.image as cat_image'))
							->whereNotNull('code')
							->get();
			//var_dump($places); exit;
		foreach ($places as $key => $place) {

			$newPlace=Place::where('title','like','%'.$place->title.'%')
				->first();
				if(!$newPlace){
			      var_dump($newPlace);
			      echo "<br>";
			      echo $place->id. " - " .$place->title;
			      echo "<br>";
			      echo $place->lng;
			      echo "<br>";
			      echo $place->lat;
			      echo "<br>";
			      $queries=DB::getQueryLog();
			      $last_query = end($queries);
			      var_dump($last_query);
			      continue;
			     // exit;
				}
			$newCategory=Category::where('code','=',$place->code)->first();
			if(!$newCategory){
				$image=explode(',',$place->cat_image);
				$newCategory=Category::create(array(
					'code'=>$place->code,
					'title'=>$place->cat_title,
					'tag'=>$place->cat_tag,
					'description'=>$place->cat_description,
					'trusted'=>$place->cat_trusted,
					'type'=>2, //place
					'image'=>trim($image[3]),
					'group_id'=>1,
				));
			}
			$newPlace->category_id=$newCategory->id;
			$newPlace->save();
		}			



			//Update categories events
		$events=$c->table('events')->leftJoin('incident_category','events.id','=','incident_id')
							->leftJoin('categories','categories.id','=','incident_category.category_id')
							->select(DB::raw('events.*,categories.code as code, categories.title as cat_title,
						 	         categories.tag as cat_tag, categories.description as cat_description,
						 	         categories.trusted as cat_trusted,categories.image as cat_image'))
							->groupBy('events.id')
							->whereNotNull('code')
							->get();

		foreach ($events as $key => $place) {	
			$newEvent=Event::where('title','like','%'.$place->title.'%')->first();
			if(!$newEvent){
				echo "No event";
				var_dump($place);
 				$queries=DB::getQueryLog();
			    $last_query = end($queries);
			    var_dump($last_query);
				continue;
			}

			$newCategory=Category::where('code','=',$place->code)
						->where('type','=',1)->first();

			if(!$newCategory){
				$image=explode(',',$place->cat_image);
				$newCategory=Category::create(array(
					'code'=>$place->code,
					'title'=>$place->cat_title,
					'tag'=>$place->cat_tag,
					'description'=>$place->cat_description,
					'trusted'=>$place->cat_trusted,
					'type'=>1, //event
					'image'=>trim($image[2]),
					'group_id'=>1,
				));
			}
			$newEvent->category_id=$newCategory->id;
			$newEvent->save();
			$newCategory->save();
		}				
		Category::where('code','=','tem')->delete();
		echo "Finish";
		
});


Route::any('payments','PaymentsController@index');

Route::get('payments-success','PaymentsController@successRequest');

Route::get('payments-cancel','PaymentsController@cancelRequest');
Route::get('renew','PaymentsController@renew');

Route::get('migrate_layers',function(){
	$maps=Map::with('layers')->get();
	foreach ($maps as $key => $map) {
		if($map->layers && isset($map->layers[0])){
			echo $map->kml_layer,"<br>";
			$map->layers[0]->kml_layer=$map->kml_layer;
			$map->layers[0]->geojson_layer=$map->geojson_layer;
			$map->layers[0]->save();
		}
	}
	echo "1";
});

Route::get('wizard','WizardController@index');
Route::get('user-exists','ApiController@userExists');

Route::get('update-icons',function(){
	foreach (glob(public_path().'/img/icons/*.png') as $filename) {
		$filename = str_replace(public_path().'/img/icons/', '', $filename);
		$exists=Icon::where('name','=',$filename)->count();
		if($exists) continue;
		Icon::create(array('name'=>$filename));
    	echo $filename."<br />";
	}
});


Route::group(array('prefix' => 'api/v1','before' => 'auth.basic'), function()
{
    Route::resource('places', 'ApiPlacesController');
    Route::post('places/import','ApiPlacesController@import');
});