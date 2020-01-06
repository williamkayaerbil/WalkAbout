<?php

class PlacesController extends \BaseController {

	public $singular_name="Place";
	public $maps;
	public $categories;
	private $jsonMaps;
	public function __construct(){
		parent::__construct();
		$this->jsonMaps= Map::getMaps();	
		$this->maps=array(""=>"Select Map")+$this->jsonMaps->lists('name','id');			
		$this->categories=Category::getCategories(2)->lists('title','id');
		$this->table=Place::getBuilder();
	}

	/**
	 * Display a listing of places
	 *
	 * @return Response
	 */
	public function index()
	{

  		if(!Input::has('filter')){
	    		Session::forget($this->name.'_filter');
	    }

		$filters=new TableFilter($this->table);
		$filters->table="places";
		$filters->addFilter("title",TableFilter::TEXT);
		$filters->addFilter("description",TableFilter::TEXT);
		$filters->addFilter("layer_id",TableFilter::SELECT);
		$filters->addFilter("deleted",TableFilter::DELETED);
		$filters->addFilter("category_id",TableFilter::SELECT);
		$filters->addFilter("layer_id",TableFilter::SELECT);
		$filters->sort();
		$filters->addCustomFilter(function($query){
			if(isset(Input::get('filter')['map_id'])){
				$map_id=Input::get('filter')['map_id'];
				if($map_id){
					$query->where('layers.map_id','=',$map_id);

				}
			}
		});
		$data=$filters->getResult()->select(DB::raw('places.*,maps.name as map_name, layers.name as layer_name, categories.title as category_title, count(events.id) as events_count'))->paginate($this->pagination);


		$layers=array();
		if(isset(Input::get('filter')['map_id']) ){
			$map_id=Input::get('filter')['map_id'];
			$map=Map::with('layers')->find($map_id);
			if($map){
				$layers=$map->layers->lists('name','id');
			}
		}

		$columns=array(
				array(
					"name"=>'id',
					"text"=>'ID',
					'orderBy'=>'id',
					'value'=>function($id){
						return   Hashids::encode($id);
					},
				    "attributes"=>'style="width:10px"',
				),
				array(
					"name"=>'title',
					"text"=>'Title',
					"value"=>function($title){
						return strip_tags($title);
					},
					'orderBy'=>'title'
				),
				array(
					"name"=>'description',
					"text"=>'Description',
					'orderBy'=>'description'
				),
				array(
						"name"=>"layer",
						"text"=>'Map',
						"value"=>function($layer){
							if(!$layer) return;
							if($layer->map->trashed()){
								return "<span class='text-danger'>".$layer->map->name."</span>";
							}
							return '<a href="'.url('admin/layers/'.$layer->map->id.'/edit').'">'.$layer->map->name.'</a>';
					},
					'orderBy'=>'map_name'
				),
				array(
					"name"=>"layer",
					"text"=>'Layer',
					"value"=>function($layer){
						if($layer->trashed()){
							return "<span class='text-danger'>".$layer->name."</span>";
						}
						return '<a href="'.url('admin/layers/'.$layer->id.'/edit').'">'.$layer->name.'</a>';
					},
					'orderBy'=>'layer_name'
				),
				array(
						"name"=>"category",
						"text"=>'Category',
						"value"=>function($category){
							if(!$category){
								return "";
							}
							if($category->trashed()){
							return "<span class='text-danger'>".$category->title."</span>";
						    }
							return '<a href="'.url('admin/categories/'.$category->id.'/edit').'">'.$category->title.'</a>';
					},
					'orderBy'=>'category_title'
				),
				array(
						"name"=>"events",
						"text"=>'Events',
						"value"=>function($events){
							$count=count($events);

							if($count>0){
								$event=$events[0];
								return '<a href="'.url('admin/events?filter[title]=&filter[description]=&filter[map_id]='.$event->place->layer->map->id.'&filter[layer_id]='.$event->place->layer->id.'&filter[place_id]='.$event->place->id.'&filter[category_id]').'">'.$count.'</a>';
							}
							return $count;
					},
					'orderBy'=>'events_count'
				),

			);
		return View::make('places.index')
		->with('data',$data)
		->with('columns',$columns)
		->with('maps',$this->maps)
		->with('categories',$this->categories)
		->with('layers',$layers);
	}
	public function create(){
		return parent::create()
		->with('maps',$this->maps)
		->with('jsMap',json_encode($this->jsonMaps))
		->with('categories',$this->categories);
	}
	public function other_places(){
		$places= Place::where('layer_id','=',Input::get('layer_id'));
		if(Input::has('place_id')){
			$places->where('places.id','!=',Input::get('place_id'));
		}
		return $places->select(
			'title',
			'lat',
			'lng')->get();
	}
	public function edit($id){
			
			$model = $this->table->with('layer')->where(Request::segment(2).'.id','=',$id)->select(Request::segment(2).'.*')->first();
			if(!$model){
				App::abort(404);
			}
			if($model->layer&&$model->layer->map_id){
				$this->categories = ApiController::getCategories($model->layer->map_id,2)->lists('title','id');
			}
			return View::make($this->name.'.create')
			->with('model',$model)
			->with('route',array('admin.'.strtolower($this->name).'.update',$model->id))
			->with('method','PUT')
			->with('name',$model->title?$model->title:$model->name)
			->with('maps',$this->maps)
			->with('jsMap',json_encode($this->jsonMaps))
			->with('categories',$this->categories);
	}

	public function export(){
	$table=Place::leftJoin('categories','categories.id','=','places.category_id')
				->leftJoin('layers','layers.id','=','places.layer_id')
				->leftJoin('maps','maps.id','=','layers.map_id')
				->leftJoin('events','events.place_id','=','places.id')
				->groupBy('places.id');


		$filters=new TableFilter($table);
		$filters->table="places";
		$filters->addFilter("title",TableFilter::TEXT);
		$filters->addFilter("description",TableFilter::TEXT);
		$filters->addFilter("layer_id",TableFilter::SELECT);
		$filters->addFilter("deleted",TableFilter::DELETED);
		$filters->addFilter("category_id",TableFilter::SELECT);
		$filters->addFilter("layer_id",TableFilter::SELECT);
		$filters->sort();
		$filters->addCustomFilter(function($query){
			if(isset(Input::get('filter')['map_id'])){
				$map_id=Input::get('filter')['map_id'];
				if($map_id){
					$query->where('layers.map_id','=',$map_id);

				}
			}
		});
		$data=$filters->getResult()->select(
		'places.title',
		'places.description',
		'places.picture',
		'places.location',
		'places.lat',
		'places.lng',
		'places.start',
		'places.end',
		'places.website',
		'places.verified',
		'categories.code',
		'places.facebook',
		'places.twitter',
		'places.instagram')->get();
		$this->writeCSV($data);
	}
	public function import(){
		if(!Input::hasFile('file')){
			Session::flash('error',"CSV File is required");
			return Redirect::back();
		}
		if(!Input::has('delimiter')){
			Session::flash('error',"Delimiter is required");
			return Redirect::back();
		}
		$data=$this->readCSV();
		 if(count($data)===0){
			Session::flash('error',"Invalid Format");
			return Redirect::back();
		}

			$layer_id=Input::get('layer_id');
 		$validator = Validator::make(
 			array('layer_id'=>$layer_id),
 			array('layer_id'=>'required'),
 			array('layer_id'=>'Layer is required'
 		));
		if(!$validator->passes()){
			return Redirect::back()->withErrors($validator)->withInput();
		}
		$categories=[];
		$overwrite=Input::has('overwrite');

		$requiredColumns=array(
			"title","lat","lng","category"
		);

		foreach ($data as $key => &$row) {
			$row["layer_id"]=$layer_id;

			if($key==0){
				foreach ($requiredColumns as $k => $column) {
					if(!isset($row[$column])){
						Session::flash('error',"The $column column is required in CSV file");
						return Redirect::back();	
					}
				}	
			}

			if(!isset($categories[$row['category']])){
				$categories[$row['category']]=Category::where('code',$row['category'])
											->where('type','=',2)->first();
			}
			if($categories[$row['category']]){
				$row['category_id']=$categories[$row['category']]->id;
			}
			unset($row["category"]);
			if(empty($row['start']) || empty($row['end'])){
				$row['permanent']=1;
			}else{
				$row['permanent']=0;
			}

			$row["start"]=$this->normalizeDate($row["start"]);
			$row["end"]=$this->normalizeDate($row["end"]);

			if($overwrite){
				$layer=Place::where('title','=',$row['title'])
					->where('layer_id','=',$layer_id)->first();
				if($layer){
					$layer->fill($row);
					$layer->save();
					unset($data[$key]);
				}
			}


		}
		if(!Input::has('overwrite')){
			$q=Place::where('layer_id','=',$layer_id)->
			where(function($q) use ($data){
				foreach ($data as $key => $row) {
					$q->orWhere('title','=',$row['title']);
				}
			});

			if($q->count()>0){
				Session::flash('error',"One or more titles are duplicates");
				return Redirect::back();
			}
		}
		try{
			if(count($data)>0){
				Place::insert($data);
			}

		}catch(Exception $e){
			if(isset($e->errorInfo[2])){
				Session::flash('error', $e->errorInfo[2]);
			}else{
				Session::flash('error',$e->getMessage());
			}

			return Redirect::back();
		}
		Session::flash('message', 'Data imported successfully');
		return Redirect::back();
	}


function getGps($exifCoord, $hemi) {

    $degrees = count($exifCoord) > 0 ? $this->gps2Num($exifCoord[0]) : 0;
    $minutes = count($exifCoord) > 1 ? $this->gps2Num($exifCoord[1]) : 0;
    $seconds = count($exifCoord) > 2 ? $this->gps2Num($exifCoord[2]) : 0;

    $flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;

    return $flip * ($degrees + $minutes / 60 + $seconds / 3600);

}

function gps2Num($coordPart) {

    $parts = explode('/', $coordPart);

    if (count($parts) <= 0)
        return 0;

    if (count($parts) == 1)
        return $parts[0];

    return floatval($parts[0]) / floatval($parts[1]);
}



function rmdir($dir) {
   if (is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if ($object != "." && $object != "..") {
         if (filetype($dir."/".$object) == "dir"){
            rrmdir($dir."/".$object);
         }else{
            unlink($dir."/".$object);
         }
       }
     }
     reset($objects);
     rmdir($dir);
  }
}


	public function	upload_photos(){
		$file=Input::file('file');
		$extension =$file->getClientOriginalExtension();
		if(!in_array(strtolower($extension), ['jpg','jpeg']))	{
			return Response::make("Invalid file extension", 409);
		}
		$destinationPath= public_path()."/img/places/tmp".Auth::user()->id.'/';
		$filename=$file->getClientOriginalName();
    	if($file->move($destinationPath, $filename)){
    		return 1;
    	}
    	return Response::make("Server error", 503);
	}
	public function save_uploaded_photos(){
		if(Input::has('accepted')){
			foreach(Input::get('accepted') as $accepted){
				$file=public_path()."/img/places/tmp".Auth::user()->id.'/'.$accepted;
				$exif=exif_read_data($file);
				$lat="";
				$lng="";
				if($exif && isset($exif["GPSLongitude"]) ){
					$lng = $this->getGps($exif["GPSLongitude"], $exif['GPSLongitudeRef']);
   					$lat = $this->getGps($exif["GPSLatitude"], $exif['GPSLatitudeRef']);
				}
				$place=Place::where('title','=',$accepted)->where('layer_id','=',Input::get('layer_id'))->first();
				$photo=str_random(6) . '_' .$accepted;
				$data=["title"=>$accepted,"lat"=>$lat,"lng"=>$lng,"layer_id"=>Input::get('layer_id'),"category_id"=>Input::get('category_id'),"picture"=>$photo];
				if($place){
					$place->fill($data);
				}else{
					$place=Place::create($data);
					$place->start=date('Y-m-d H:i:s');
					$place->end=date('Y-m-d H:i:s');
				}
				$place->save();
				rename($file,public_path()."/img/places/".$photo);

			}
		}
		$this->rmdir(public_path()."/img/places/tmp".Auth::user()->id);
	}

}
