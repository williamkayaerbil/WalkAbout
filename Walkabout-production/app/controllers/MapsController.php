<?php

class MapsController extends \BaseController {

	public $singular_name="Map";
	public $groups=array();
	public $users=array();
	public function __construct(){
		parent::__construct();
		if(Auth::user()->hasRole("Admin")){
			$this->groups=Group::all()->lists('name','id');
			if(Input::has('group_id')){
				$this->users=User::where('group_id','=',Input::get('group_id'))->get()->lists('name','id');
			}
		}else if(Auth::user()->hasRole('Account Admin')){
			$this->groups=Auth::user()->groups()->select('groups.*')->get()->lists('name','id');
			if(Input::has('group_id')){//TODO
				$this->user=User::whereIn('group_id',Auth::user()->getGroupIds())->where('group_id','=',Input::get('group_id'))->select('users.*')->get()->lists('name','id');
			}

		}
		else if(Auth::user()->can("manage_maps")){
			$this->users=User::where('group_id','=',Auth::user()->group_id)->get()->lists('name','id');
		}

		$this->table=Map::getBuilder();

	}
	/**
	 * Display a listing of maps
	 *
	 * @return Response
	 */
	private $positions=array(
		''=>'NONE',
		1=>'TOP_LEFT',
		2=>'TOP_CENTER',
		3=>'TOP_RIGHT',
		4=>'LEFT_CENTER',
		5=>'LEFT_TOP',
		6=>'LEFT_BOTTOM',
		7=>'RIGHT_TOP',
		8=>'RIGHT_CENTER',
		9=>'RIGHT_BOTTOM',
		10=>'BOTTOM_LEFT',
		11=>'BOTTOM_CENTER',
		12=>'BOTTOM_RIGHT'
	);

	public function index()
	{
  		if(!Input::has('filter')){
	    		Session::forget($this->name.'_filter');
	    }
		

	
		$filters=new TableFilter($this->table);
		$filters->table="maps";
		$filters->addFilter("name",TableFilter::TEXT);
		$filters->addFilter("description",TableFilter::TEXT);
		$filters->addFilter("user_id",TableFilter::SELECT);
		$filters->addFilter("deleted",TableFilter::DELETED);
		$filters->addCustomFilter(function($query){
			if(isset(Input::get('filter')['group_id'])){
				$group_id=Input::get('filter')['group_id'];
				if($group_id){
					$query
					->where('users.group_id','=',$group_id)
					->select('maps.*');
				}
			}
		});
		if(isset(Input::get('filter')['group_id'])){
			$group_id=Input::get('filter')['group_id'];
			if($group_id){
				$this->users=User::where('group_id','=',$group_id)->get()->lists('name','id');
			}
		}
		$filters->sort();
		$data=$filters->getResult()->select(DB::raw('maps.*,count(layers.id) as layers_count, groups.name as group_name'))->paginate($this->pagination);


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
					"attributes"=>'style="width:300px"',
					"value"=>function($description){
						if(strlen($description)>100){
							return strip_tags(substr($description, 0,97)."...");
						}
						return $description;
					},
					'orderBy'=>'description'	
					

				),
				array(
					"name"=>"hash",
					"text"=>'URL',

					"value"=>function($value){
						return '<a target="_blank" href="'.url('map',$value).'">Go to URL</a>';
					},
				
				),

				array(
						"name"=>"layers",
						"text"=>'Layers',
						"value"=>function($layers){
							if(!$layers) return "";
							$count=count($layers);
							if($count>0){
								$layer=$layers[0];
								if(!$layer || !$layer->map) return "";
								return '<a href="'.url('admin/layers?filter[name]=&filter[description]=&filter[map_id]='.$layer->map->id.'&filter[category_id]').'">'.$count.'</a>';
							}
							return $count;
					},
					'orderBy'=>'layers_count'
				),

			);
		if(Auth::user()->hasRole("Admin")){
				$columns[]=array(
					"name"=>"user",
					"text"=>'Group',
					"value"=>function($user){
						if(!$user) return "";
						if($user->group->trashed()){
							return "<span class='text-danger'>".$user->group->name."</span>";
						}
							return '<a href="'.url('admin/groups/'.$user->group->id.'/edit').'">'.$user->group->name.'</a>';
					},
					'orderBy'=>'group_name'
				
				);
		}
		if(Auth::user()->can('manage_maps')){
			$columns[]=array(
					"name"=>"user",
					"text"=>'Owner',
					"value"=>function($user){
						if(!$user) return "";
						if($user->trashed()){
							return "<span class='text-danger'>".$user->name."</span>";
						}
						return '<a href="'.url('admin/users/'.$user->id.'/edit').'">'.$user->name.'</a>';
					},
					'orderBy'=>'users.name'
				
				);
		}
		return View::make('maps.index')
		->with('data',$data)
		->with('columns',$columns)
		->with('groups',$this->groups)
		->with('users',$this->users);
	}

	public function create(){

		if(Auth::user()->getAvailableMaps()=="0"){
			return Redirect::to('admin/maps');
		}
		$this->users=User::where('group_id','=',Auth::user()->group_id)->get()->lists('name','id');
		return parent::create()
			->with('positions',$this->positions)
			->with('groups',$this->groups)
			->with('users',$this->users);
	}
	public function edit($id){
			
			$model = $this->table->with('user')->where(Request::segment(2).'.id','=',$id)->select(Request::segment(2).'.*')->first();
			if(!$model){
				App::abort(404);
			}
			$users = array();
			if($model->user && $model->user->group && $model->user->group->users){
				$users = $model->user->group->users->lists('name','id');
			}else{
				$users = User::all()->lists('name','id');
			}

			return View::make($this->name.'.create')
			->with('model',$model)
			->with('route',array('admin.'.strtolower($this->name).'.update',$model->id))
			->with('method','PUT')
			->with('name',$model->title?$model->title:$model->name)
			->with('positions',$this->positions)
			->with('users',$users)
			->with('groups',$this->groups);
			
	}

	/**
	 * Update the specified map in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$map = $this->table->where(Request::segment(2).'.id',$id)->select("maps.*")->first();
		if(!$map){
				App::abort(404);
		}
		$validator = Validator::make($data = Input::all(), Map::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}
		$destinationPath= public_path();
		$files=['header_logo','menu_logo','place_logo','event_logo'];
		foreach ($files as $key) {	
			if(! Input::hasFile($key)) {
				unset($data[$key]);
				continue;
			}
			$file= Input::file($key);

			//if(!$map->{$key}){
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
			if(!empty($map->{$key})){
					$old_filename=$destinationPath.$folder.$map->{$key};
				if(file_exists($old_filename) && !is_dir($old_filename)){
					unlink($old_filename);
				}	
			}
			
			
			$file->move($destinationPath.$folder, $filename);
		}
	

			$logos=['header_logo_url','menu_logo_url','place_logo_url','event_logo_url'];
			foreach($logos as $logo){

				if(!empty($data[$logo]) && strpos($data[$logo], 'http')===false){
						$data[$logo]='http://'.$data[$logo];
				}
			}
			if(Input::has('for_all')){
				$logo=isset($data['header_logo'])?$data['header_logo']:$map->header_logo;
				$data['menu_logo'] =$logo;
				$data['place_logo'] =$logo;
				$data['event_logo'] =$logo;
				
				$data['menu_logo_url'] =$data['header_logo_url'];
				$data['place_logo_url'] =$data['header_logo_url'];
				$data['event_logo_url'] =$data['header_logo_url'];
			}

			if(isset($data['menu_name'])){
				$names=$data['menu_name'];
				$urls=$data['website_name'];

				$menu_options=[];
				foreach($names as $k=>$name){
					if(empty($name)||empty($urls[$k])){
						unset($names[$k]);
						unset($urls[$k]);
						continue;
					}
					

					if(strpos($urls[$k], 'http')===false){
						$urls[$k]='http://'.$urls[$k];
					}
					$menu_options[]=array(
						"name"=>$name,
						"url"=>$urls[$k]
					);
				}
				if(count($menu_options)>0){
					$data["menu_options"]=json_encode($menu_options);
				}
			}
			
		
		$data['navigation_event']=isset($data['navigation_event']);
		$data['click_event']=isset($data['click_event']);
		$map->update($data);
		$getParams=  Session::has($this->name.'_filter') 
		? ('?'.http_build_query(Session::get($this->name.'_filter'))) : "";
			return Redirect::to('admin/'.Request::segment(2).$getParams);
	}

	public function downloadFile($url,$name=null){
		if($name==null){
			$name=str_random(10).'.jpg';	
		}
		$img=public_path()."/img/map_logos/".$name;
		
		file_put_contents($img, file_get_contents($url));
		return $name;
	}

	public function save($model,$data){
			if(Auth::user()->getAvailableMaps()=="0"){
				return Redirect::to('admin/maps');
			}
			
			$uploadSuccess=true;
		try{
			$files=['header_logo','menu_logo','place_logo','event_logo'];
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
			$logos=['header_logo_url','menu_logo_url','place_logo_url','event_logo_url'];
			foreach($logos as $logo){

				if(!empty($data[$logo]) && strpos($data[$logo], 'http')===false){
						$data[$logo]='http://'.$data[$logo];
				}
			}
		

			if(Input::has('for_all')){
				$data['menu_logo'] =$data['header_logo'];
				$data['place_logo'] =$data['header_logo'];
				$data['event_logo'] =$data['header_logo'];

				$data['menu_logo_url'] =$data['header_logo_url'];
				$data['place_logo_url'] =$data['header_logo_url'];
				$data['event_logo_url'] =$data['header_logo_url'];
			}
			if(!isset($data["user_id"])){
				$data["user_id"]=Auth::user()->id;
			}
			if(isset($data['menu_name'])){
				$names=$data['menu_name'];
				$urls=$data['website_name'];

				$menu_options=[];
				foreach($names as $k=>$name){
					if(empty($name)||empty($urls[$k])){
						unset($names[$k]);
						unset($urls[$k]);
						continue;
					}
					

					if(strpos($urls[$k], 'http')===false){
						$urls[$k]='http://'.$urls[$k];
					}
					$menu_options[]=array(
						"name"=>$name,
						"url"=>$urls[$k]
					);
				}
				if(count($menu_options)>0){
					$data["menu_options"]=json_encode($menu_options);
				}
			}
			$data['navigation_event']=isset($data['navigation_event']);
			$data['click_event']=isset($data['click_event']);
			$last=Map::orderBy('created_at', 'desc')->first();
			if($last){
				$data['hash']=md5(($last->id+1+time()).rand(0,1000));
			}
			
			$m=$model::create($data);
			$m->hash=md5(($m->id+time()).rand(0,1000));
			Layer::create(["name"=>$m->name,'description'=>$m->description,'map_id'=>$m->id]);
			$m->save();
			
$getParams=  Session::has($this->name.'_filter') 
		? ('?'.http_build_query(Session::get($this->name.'_filter'))) : "";
			return Redirect::to('admin/'.Request::segment(2).$getParams);
			}catch(Exception $e){
				throw $e;
				return Redirect::back()->withErrors(array($e->getMessage()))->withInput();
			}

		}

   

    public function export(){

		$filters=new TableFilter($this->table);
		$filters->addFilter("name",TableFilter::TEXT);
		$filters->addFilter("description",TableFilter::TEXT);
		$filters->addFilter("user_id",TableFilter::SELECT);
		$filters->addFilter("deleted",TableFilter::DELETED);
		$filters->addCustomFilter(function($query){
			if(isset(Input::get('filter')['group_id'])){
				$group_id=Input::get('filter')['group_id'];
				if($group_id){
					$query
					->where('users.group_id','=',$group_id)
					->select('maps.*');
				}
			}
		});
		if(isset(Input::get('filter')['group_id'])){
			$group_id=Input::get('filter')['group_id'];
			if($group_id){
				$this->users=User::where('group_id','=',$group_id)->get()->lists('name','id');
			}
		}
		$filters->sort();
		$data=$filters->getResult()->select(DB::raw('maps.*,count(layers.id) as layers_count, groups.name as group_name'))->get();
	
		$this->writeCSV($data);
    }
	public function import(){
		if(Auth::user()->getAvailableMaps()=="0"){
			return Redirect::to('admin/maps');
		}
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
		$columns=array_keys($data);

		$user_id="";
		if(Auth::user()->can("manage_maps")){
			$user_id=Input::get('user_id');
		}else{
			$user_id=Auth::user()->id;
		}
		
 		$validator = Validator::make( 
 			array('user_id'=>$user_id),
 			array('user_id'=>'required'),
 			array('user_id'=>'User is required'
 		));
		if(!$validator->passes()){
			return Redirect::back()->withErrors($validator)->withInput();
		}
		$overwrite=Input::has('overwrite');

		$requiredColumns=array(
			'hash','name','center','zoom'
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


			$row["user_id"]=$user_id;
			if($overwrite){
				$map=Map::where('hash','=',$row['hash'])->first();
				if($map){
					$map->fill($row);
					$map->save();	
					unset($data[$key]);
				}
			}
			if(trim($row['hash'])=="__RANDOM__"){
				$row['hash']=md5((rand(0,1000)+time()).rand(0,1000));
			}
			if(isset($row['header_logo'])){
				$row['header_logo']=$this->downloadImage($row['header_logo'],public_path()."/img/map_logos/");
			}
			if(isset($row['menu_logo'])){
				$row['menu_logo']=$this->downloadImage($row['menu_logo'],public_path()."/img/map_logos/");	
			}
			if(isset($row['place_logo'])){
				$row['place_logo']=$this->downloadImage($row['place_logo'],public_path()."/img/map_logos/");	
			}
			if(isset($row['event_logo'])){
				$row['event_logo']=$this->downloadImage($row['event_logo'],public_path()."/img/map_logos/");	
			}
			if(isset($row['kml_layer'])){
				$row['kml_layer']=$this->downloadImage($row['kml_layer'],public_path()."/kml_layers/");	
			}
			if(isset($row['geojson_layer'])){
				$row['geojson_layer']=$this->downloadImage($row['geojson_layer'],public_path()."/geojson_layers/");	
			}
		
		}

		if(!Input::has('overwrite')){
			$q=Map::where(function($q) use ($data){
				foreach ($data as $key => $row) {
					$q->orWhere('hash','=',$row['hash']);
				}
			});
			
			if($q->count()>0){
				Session::flash('error',"One or more hashes are duplicates");
				return Redirect::back();
			}
		}	
		try{
			if(count($data)>0){
				Map::insert($data);
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
