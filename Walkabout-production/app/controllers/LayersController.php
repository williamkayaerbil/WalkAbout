<?php

class LayersController extends \BaseController {
	private $maps=array();
	public $singular_name="Layer";
	
	public function __construct(){

		parent::__construct();
	
		$this->maps=array(""=>"Select Map")+Map::getMaps()->lists('name','id');
		$this->table=Layer::getBuilder();

	}
	/**
	 * Display a listing of layers
	 *
	 * @return Response
	 */

	public function index()
	{

  		if(!Input::has('filter')){
	    	Session::forget($this->name.'_filter');
	    }
	
		$filters=new TableFilter($this->table);
		$filters->table="layers";
		$filters->addFilter("name",TableFilter::TEXT);
		$filters->addFilter("description",TableFilter::TEXT);
		$filters->addFilter("map_id",TableFilter::SELECT);
		$filters->addFilter("deleted",TableFilter::DELETED);
		$filters->sort();
		$data=$filters->getResult()->select(DB::raw('layers.*, count(places.id) as places_count, maps.name as map_name'))->paginate($this->pagination);

		$columns=array(
			
				array(
					"name"=>'name',
					"text"=>'Name',
					"attributes"=>'style="width:30px"',
					'orderBy'=>'name'
				),
				array(
					"name"=>'description',
					"text"=>'Description',
					'orderBy'=>'description'
				),
				array(
					"name"=>"map",
					"text"=>'Map',
					"value"=>function($value){
							if(!is_object($value)) return;
							if($value->trashed()){
								return '<span class="text-danger">'.$value->name.'</span>';
							}
						return '<a href="'.url('admin/maps/'.$value->id.'/edit').'">'.$value->name.'</a>';
					},
					'orderBy'=>'map_name'
				
				),
				array(
						"name"=>"places",
						"text"=>'Places',
						"value"=>function($places){
							if(!$places) return "";
							$count=count($places);
							if($count>0){
								$place=$places[0];
								if(!$place || !$place->layer || !$place->layer->map) return "";
								return '<a href="'.url('admin/places?filter[title]=&filter[description]=&filter[map_id]='.$place->layer->map->id.'&filter[layer_id]='.$place->layer->id.'&filter[category_id]').'">'.$count.'</a>';
							}
							return $count;
					},
					'orderBy'=>'places_count'
				),


			);
		return View::make('layers.index')
			->with('data',$data)
			->with('columns',$columns)
			->with('maps',$this->maps);
	}
	public function create(){
		return parent::create()
		->with('maps',$this->maps);
	}

	public function save($model,$data){
		try{
			$files=['geojson_layer','kml_layer'];
			foreach ($files as $key) {
			
			
				if(! Input::hasFile($key)) {
					unset($data[$key]);
					continue;
				}
				$folder='/img/map_logos/';
				if($key=="kml_layer"){
					$folder='/kml_layers/';
				}
				if($key=="geojson_layer"){
					$folder="/geojson_layers/";
				}
				$destinationPath= public_path().$folder;
				$file= Input::file($key);
				$filename=str_random(6) . '_' . $file->getClientOriginalName();
			    $file->move($destinationPath, $filename);
				$data[$key]=$filename;
			}

			$layer=Layer::create($data);

			//$layer->save();
			//var_dump($layer);exit;
			$getParams=  Session::has($this->name.'_filter') 
			? ('?'.http_build_query(Session::get($this->name.'_filter'))) : "";
			return Redirect::to('admin/'.Request::segment(2).$getParams);
			}catch(Exception $e){
				throw $e;
				return Redirect::back()->withErrors(array($e->getMessage()))->withInput();
			}
	}

	public function update($id){
		$layer = $this->table->where(Request::segment(2).'.id',$id)->select(Request::segment(2).'.*')->first();
		if(!$layer){
				App::abort(404);
		}
		$validator = Validator::make($data = Input::all(), Layer::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}
		$destinationPath= public_path();
		$files=['kml_layer','geojson_layer'];
		foreach ($files as $key) {	
			if(! Input::hasFile($key)) {
				unset($data[$key]);
				continue;
			}
			$file= Input::file($key);

			//if(!$layer->{$key}){
				$filename=str_random(6) . '_' . $file->getClientOriginalName();
				$data[$key]=$filename;
			//}else{
				
				
			//}
			$folder='/img/map_logos/';
			if($key=="kml_layer"){
				$folder='/kml_layers/';
			}
			if($key=="geojson_layer"){
				$folder="/geojson_layers/";
			}
			if(!empty($layer->{$key})){
					$old_filename=$destinationPath.$folder.$layer->{$key};
				if(file_exists($old_filename) && !is_dir($old_filename)){
					unlink($old_filename);
				}	
			}
			
			
			$file->move($destinationPath.$folder, $filename);
		}
		$layer->update($data);
		$getParams=  Session::has($this->name.'_filter') 
		? ('?'.http_build_query(Session::get($this->name.'_filter'))) : "";
			return Redirect::to('admin/'.Request::segment(2).$getParams);
	}

	/**
	 * Show the form for editing the specified layer.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id){

		return parent::edit($id)
			->with('maps',$this->maps);
	}

	public function export(){

	

		$filters=new TableFilter($this->table);
		$filters->table="layers";
		$filters->addFilter("name",TableFilter::TEXT);
		$filters->addFilter("description",TableFilter::TEXT);
		$filters->addFilter("map_id",TableFilter::SELECT);
		$filters->addFilter("deleted",TableFilter::DELETED);
		$filters->sort();
		$data=$filters->getResult()->select(DB::raw('layers.*, count(places.id) as places_count, maps.name as map_name'))->get();
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
		
			$map_id=Input::get('map_id');
 		$validator = Validator::make( 
 			array('map_id'=>$map_id),
 			array('map_id'=>'required'),
 			array('map_id'=>'Map is required'
 		));
		if(!$validator->passes()){
			return Redirect::back()->withErrors($validator)->withInput();
		}
		$overwrite=Input::has('overwrite');
		foreach ($data as $key => &$row) {
			if($key==0){
				if(!isset($row['name'])){
					Session::flash('error',"The name column is required in CSV file");
					return Redirect::back();	
				}
			}

			$row["map_id"]=$map_id;
			if($overwrite){
				$layer=Layer::where('name','=',$row['name'])
					->where('map_id','=',$map_id)->first();
				if($layer){
					$layer->fill($row);
					$layer->save();	
					unset($data[$key]);
				}
			}
		

		}
		if(!Input::has('overwrite')){
			$q=Layer::where('map_id','=',$map_id)->
			where(function($q) use ($data){
				foreach ($data as $key => $row) {
					$q->orWhere('name','=',$row['name']);
				}
			});
			
			if($q->count()>0){
				Session::flash('error',"One or more names are duplicates");
				return Redirect::back();
			}
		}	
		try{
			if(count($data)>0){
				Layer::insert($data);	
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


	

}
