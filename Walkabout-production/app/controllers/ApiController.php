<?php

class ApiController extends Controller {

    public static function getLayers($parent_id){
    	return Layer::getBuilder()->where('map_id','=',$parent_id)->select("layers.*")->get();
    }
	public function layers(){
		return self::getLayers(Input::get('parent_id'));
	}
	public static function getPlaces($parent_id){
		return Place::getBuilder()->where('layer_id','=',$parent_id)->select('places.*')->get();
	}
	public function places(){
		return self::getPlaces(Input::get('parent_id'));

	}

	public function users(){
		if(Auth::user()->can('manage_users')){
			return User::where('group_id','=',Input::get('parent_id'))->get();
		}
	}
	public static function getCategories($map_id,$type){
		$map = Map::withTrashed()->find($map_id);
		$user = User::withTrashed()->find($map->user_id);
		if(!$user){
			return Category::where('type','=',$type)->get();
		}
		$group=$user->group;
		return $group->categories()->where('type','=',$type)->get();
	}
	public function categories(){
		$map_id = Input::get('map_id');
		$type = Input::get('type');
		return self::getCategories($map_id,$type);
	}


	public function data($hash){
		//if(!Input::has('formdata'))return "Invalid Hash";
		//$data=json_decode(Input::get('formdata'));
		$active=true;
		$map=Map::where('maps.hash','=',$hash)->get()->first();	
		
		$user=User::find($map->user_id);
		if($user){

			if($user->hasRole('Account Admin')){
				if($user->type==2){ //Paypal user
					$active=$user->isActive();
				}
				
			}else if(!$user->hasRole('Admin')){
				if($u=$user->group->user){
					$active=$u->isActive();
				}
				
				
			}

		}
		

		if(!$active){
			return "Account suspended";
		}



		$map->zoom_ctrl=$map->zoom_ctrl_pos!=null;
		$map->pan_ctrl=$map->pan_ctrl_pos!=null;
		$map->maptype_ctrl=$map->maptype_ctrl_pos!=null;
		$map->streetview_ctrl=$map->streetview_ctrl_pos!=null;




		$places=[];
		$categories_id=array();
		$layers=Layer::where('map_id','=',$map->id)->orderBy('order','asc')->get();
		$map->layers=$layers;
		$layers_id=array();
		foreach ($layers as $key => $layer) {
			$layers_id[]=$layer->id;
		}
		$places=Place::with('events')->whereIn('layer_id',$layers_id)->orderBy('start','asc')->get();

		foreach($places as $place){
			$categories_id[$place->category_id]=$place->category_id;
			if($place->events){
				foreach ($place->events as $event) {
					$categories_id[$event->category_id]=$event->category_id;
				}
			}
		}
		
		$data=new StdClass();
		$data->map=$map;
		$data->categories=Category::whereIn('id',$categories_id)->get();
		$data->places=$places;
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		    $ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
		    $ip = $_SERVER['REMOTE_ADDR'];
		}
		$data->client=$ip;
   		return json_encode($data);
	}
	public function saveInteractions(){
		$interactions=Input::get('interactions');
		if(is_array($interactions)){
			foreach ($interactions as $key => $interaction) {
				$interaction["time"]=date('Y-m-d H:i:s');
				Interaction::create($interaction);
			}
		}
		return "{code: 1}";
	}

	public function getIcons(){
		$result=array();
		 foreach (Icon::all() as $key => $icon){
		 	$result[$key]["name"]=$icon->name;
		 	$result[$key]["url"]=asset('img/icons/'.$icon->name);
		}
		return $result;
	}
	public function getIcon(){
		$icon = Input::get('icon');
		$r = Input::get('r');
		$g = Input::get('g');
		$b = Input::get('b');

	 	
		$file=public_path('img/categories/'.$icon);
		if(!file_exists($file)) App::abort(404);
		header('Content-Type: image/png');
		$im = imagecreatefrompng($file);

		for($x = 0; $x < imagesx($im); ++$x)
	    {
	        for($y = 0; $y < imagesy($im); ++$y)
	        {
	            $index = imagecolorat($im, $x, $y);
	            $rgb = imagecolorsforindex($im,$index);
	            $color = imagecolorallocate($im, $r,$g,$b);
	            if($rgb['alpha'] == 0){
	            	imagesetpixel($im, $x, $y, $color);
	            }

	        }
	    }

		/* Negative values, don't edit */
		//$rgb = array(255-$rgb[0],255-$rgb[1],255-$rgb[2]);



		//imagefilter($im, IMG_FILTER_NEGATE); 
		//imagefilter($im, IMG_FILTER_COLORIZE, $rgb[0], $rgb[1], $rgb[2]); 
		//imagefilter($im, IMG_FILTER_NEGATE); 

		imagealphablending( $im, false );
		imagesavealpha( $im, true );
		imagepng($im);
		imagedestroy($im);
		exit;
	}

	public function userExists(){
		return count(User::where('email','=',trim(Input::get('email')))->get());
	}
}
