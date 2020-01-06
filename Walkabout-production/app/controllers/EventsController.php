<?php

class EventsController extends \BaseController {

	public function __construct(){
		parent::__construct();
		$this->categories=Category::getCategories(1)->lists('title','id');
		$this->maps=array(""=>"Select Map")+Map::getMaps()->lists('name','id');
		$this->table=Event::getBuilder();
	}
	/**
	 * Display a listing of events
	 *
	 * @return Response
	 */
	public $singular_name="Event";
	public function index()
	{
		  if(!Input::has('filter')){
	    	Session::forget($this->name.'_filter');
	    }
		$filters=new TableFilter($this->table);
		$filters->table="events";
		$filters->addFilter("title",TableFilter::TEXT);
		$filters->addFilter("description",TableFilter::TEXT);
		$filters->addFilter("deleted",TableFilter::DELETED);
		$filters->addFilter("category_id",TableFilter::SELECT);
		$filters->addFilter("place_id",TableFilter::SELECT);
		$filters->sort();
		$filters->addCustomFilter(function($query){  //Filter by layer
			if(isset(Input::get('filter')['layer_id'])){
				$layer_id=Input::get('filter')['layer_id'];
				if($layer_id){
					$query->where('places.layer_id','=',$layer_id)
					->select('events.*');
				}
			}
		});

		$filters->addCustomFilter(function($query){  //Filter by map
			if(isset(Input::get('filter')['map_id'])){
				$map_id=Input::get('filter')['map_id'];
				$layer_id=Input::get('filter')['layer_id'];
				if($map_id && !$layer_id){
					$query->where('layers.map_id','=',$map_id);
				}
			}
		});
		$data=$filters->getResult()->select(DB::raw('events.*,maps.name as map_name, layers.name as layer_name, places.title as place_title, categories.title as category_title'))->paginate($this->pagination);



		$layers=array();
		if(isset(Input::get('filter')['map_id']) ){
			$map_id=Input::get('filter')['map_id'];
			$map=Map::with('layers')->find($map_id);
			if($map){
				$layers=$map->layers->lists('name','id');
			}
		}
		$places=array();
		if(isset(Input::get('filter')['layer_id']) ){
			$layer_id=Input::get('filter')['layer_id'];
			$layer=Layer::with('places')->find($layer_id);
			if($layer){
				$places=$layer->places->lists('clean_title','id');
			}
		}


		$columns=array(
				array(
					"name"=>'title',
					"text"=>'Title',
					"attributes"=>'style="width:30px"',
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
						"name"=>"place",
						"text"=>'Map',
						"value"=>function($place){
							if(!$place  || !$place->layer || !$place->layer->map) return "";
							if($place->layer->map->trashed()){
								return "<span class='text-danger'>".strip_tags($place->layer->map->name)."</span>";
							}
							return '<a href="'.url('admin/maps/'.$place->layer->map->id.'/edit').'">'.strip_tags($place->layer->map->name).'</a>';						
					},
					'orderBy'=>'map_name'
				),
				array(
					"name"=>"place",
					"text"=>'Layer',
					"value"=>function($place){
						if(!$place || !$place->layer) return "";
							if($place->layer->trashed()){
								return "<span class='text-danger'>".strip_tags($place->layer->name)."</span>";
							}
						return '<a href="'.url('admin/layers/'.$place->layer->id.'/edit').'">'.strip_tags($place->layer->name).'</a>';						
					},
					'orderBy'=>'layer_name'
				),
				array(
					"name"=>"place",
					"text"=>'Place',
					"value"=>function($place){
						if(!$place ) return "";
							if($place->trashed()){
								return "<span class='text-danger'>".strip_tags($place->title)."</span>";
							}
						return '<a href="'.url('admin/places/'.$place->id.'/edit').'">'.strip_tags($place->title).'</a>';
					},
					'orderBy'=>'place_title'
				),
				array(
						"name"=>"category",
						"text"=>'Category',
						"value"=>function($category){
							if(!$category){
								return "";
							}
							
							if($category->trashed()){
								return "<span class='text-danger'>".strip_tags($category->title)."</span>";
							}
							return '<a href="'.url('admin/categories/'.$category->id.'/edit').'">'.$category->title.'</a>';
					},
					'orderBy'=>'category_title'
				),
			
			);
		
		return View::make('events.index')
		->with('data',$data)
		->with('columns',$columns)
		->with('data',$data)
		->with('columns',$columns)
		->with('maps',$this->maps)
		->with('categories',$this->categories)
		->with('layers',$layers)
		->with('places',$places);
	}

	public function create(){
		return parent::create()
		->with('maps',$this->maps)
		->with('categories',$this->categories);
	}
	public function edit($id){

			$model = $this->table->with('place')->where(Request::segment(2).'.id','=',$id)->select(Request::segment(2).'.*')->first();
			if(!$model){
				App::abort(404);
			}
			if($model->place && $model->place->layer && $model->place->layer->map_id){
				$this->categories = ApiController::getCategories($model->place->layer->map_id,1)->lists('title','id');
			}
			return View::make($this->name.'.create')
			->with('model',$model)
			->with('route',array('admin.'.strtolower($this->name).'.update',$model->id))
			->with('method','PUT')
			->with('name',$model->title?$model->title:$model->name)
			->with('maps',$this->maps)
			->with('categories',$this->categories);
	}


	public function export(){
	$filters=new TableFilter(Event::getBuilder());
		$filters->table="events";
		$filters->addFilter("title",TableFilter::TEXT);
		$filters->addFilter("description",TableFilter::TEXT);
		$filters->addFilter("deleted",TableFilter::DELETED);
		$filters->addFilter("category_id",TableFilter::SELECT);
		$filters->addFilter("place_id",TableFilter::SELECT);
		$filters->sort();
		$filters->addCustomFilter(function($query){  //Filter by layer
			if(isset(Input::get('filter')['layer_id'])){
				$layer_id=Input::get('filter')['layer_id'];
				if($layer_id){
					$query->where('places.layer_id','=',$layer_id)
					->select('events.*');
				}
			}
		});

		$filters->addCustomFilter(function($query){  //Filter by map
			if(isset(Input::get('filter')['map_id'])){
				$map_id=Input::get('filter')['map_id'];
				$layer_id=Input::get('filter')['layer_id'];
				if($map_id && !$layer_id){
					$query->where('layers.map_id','=',$map_id);
				}
			}
		});
		$data=$filters->getResult()->select(DB::raw('events.*,maps.name as map_name, layers.name as layer_name, places.title as place_title, categories.code as category_code,categories.title as category_title'))->get();
		
		
		foreach ($data as $key => $value) {
			unset($value->place);
			$value->title=strip_tags($value->title);
			$value->place_id=Hashids::encode($value->place_id);
		}

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
		
	
		$categories=[];
		$overwrite=Input::has('overwrite');

		$requiredColumns=array(
			"place","title","category"
		);
		foreach ($data as $key => &$row) {

			if($key==0){
				foreach ($requiredColumns as $k => $column) {
					if(!isset($row[$column])){
						Session::flash('error',"The $column column is required in CSV file");
						return Redirect::back();	
					}
				}	
			}

		

			$place_id=Hashids::decode($row['place']);

			if(count($place_id)==0){
				Session::flash('error',"Invalid place GUID");
				return Redirect::back();
			}
			$place=Place::find($place_id[0]);

			if($place==null){
			    Session::flash('error',"Invalid place GUID");
				return Redirect::back();
			}
			$row["place_id"]=$place_id[0];
			if(!isset($categories[$row['category']])){
				$categories[$row['category']]=Category::where('code',$row['category'])
											->where('type','=',1)->first();
			}
			if($categories[$row['category']]){
				$row['category_id']=$categories[$row['category']]->id;
			}

			$row["start"]=$this->normalizeDate($row["start"]);
			$row["end"]=$this->normalizeDate($row["end"]);
			//var_dump($row); exit;

			unset($row["category"]);
			unset($row['place']);
			if($overwrite){
				$event=Event::where('title','=',$row['title'])
					->where('place_id','=',$place_id)->first();
				if($event){
					$event->fill($row);
					$event->save();	
					unset($data[$key]);
				}
			}
		

		}
		if(!Input::has('overwrite')){
			$q=Event::where('place_id','=',$place_id)->
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
				Event::insert($data);	
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
