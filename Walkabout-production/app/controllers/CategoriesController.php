<?php

class CategoriesController extends \BaseController {

	/**
	 * Display a listing of categories
	 *
	 * @return Response
	 */
	public $singular_name="Category";
	public $groups=array();
	public function __construct(){
		parent::__construct();
		if(Auth::user()->hasRole("Admin")){
			$this->groups=Group::all()->lists('name','id');
		}else{
			$this->groups=Auth::user()->groups->lists('name','id');
		}

		$this->table=Category::getBuilder();

		
	}
	public function index()
	{	

	    if(!Input::has('filter')){
	    	Session::forget($this->name.'_filter');
	    }

		$filters=new TableFilter($this->table);
		$filters->table="categories";
		$filters->addFilter("type",TableFilter::SELECT);
		$filters->addFilter("title",TableFilter::TEXT);
		$filters->addFilter("code",TableFilter::TEXT);
		$filters->addFilter("tag",TableFilter::TEXT);
		$filters->addFilter("description",TableFilter::TEXT);
		$filters->addFilter("group_id",TableFilter::SELECT);
		$filters->addFilter("deleted",TableFilter::DELETED);
		$filters->sort();
		/*if($filter["status"]){

		}*/

		$data=$filters->getResult()->select(DB::raw('categories.*, groups.name as group_name'))->paginate($this->pagination);
		$columns=array(
		
			array(
				"name"=>'code',
				"text"=>'Code',
				"attributes"=>'style="width:30px"',
				'orderBy'=>'code'
			),
			array(
				"name"=>'title',
				"text"=>'Title',
				'orderBy'=>'title'
			),
			array(
				"name"=>'tag',
				"text"=>'Tag',
				'orderBy'=>'tag'
			),
			array(
				"name"=>'description',
				"text"=>'Description',
				'orderBy'=>'categories.description'
			),
			array(
				"name"=>"trusted",
				"text"=>'Trusted',
				"value"=>function($value){
					return $value==1?"Yes":"No";
				},
				'orderBy'=>'trusted'
			
			),
			array(
				"name"=>"type",
				"text"=>'Type',
				"value"=>function($value){
					return $value==1?"Event":"Place";
				},
				'orderBy'=>'type'
			
			),
		);

		if(Auth::user()->hasRole("Admin")){
			$columns[]=	array(
				"name"=>"group",
				"text"=>'Group',
				"value"=>function($group){
					if($group->trashed()){
						return "<span class='text-danger'>".$group->name."</span>";
					}
					return '<a href="'.url('admin/groups/'.$group->id.'/edit').'">'.$group->name.'</a>';
				},
			'orderBy'=>'group_name'
			
			);
		}
		return View::make('categories.index')
		->with('data',$data)
		->with('columns',$columns)
		->with('groups',$this->groups);
	}   

	public function create(){
		return parent::create()->with('groups',$this->groups);
	}
	public function edit($id){
		return parent::edit($id)->with('groups',$this->groups);
	}



public function save($model,$data){
		try{
		if(Input::get('icon')){
		if (!File::exists(public_path()."/img/categories/".Input::get('icon')))
			{
			    copy(public_path()."/img/icons/".Input::get('icon'),public_path()."/img/categories/".Input::get('icon'));
			}
			$data["image"]=Input::get('icon');
		}else{
			$destinationPath= public_path()."/img/".Request::segment(2);
			$uploadSuccess=true;
			
			foreach ($_FILES as $key => $file) {
				if(! Input::hasFile($key)) {
					unset($data[$key]);
					continue;
				}
				$file= Input::file($key);
				$filename=str_random(6) . '_' . $file->getClientOriginalName();
			    $file->move($destinationPath, $filename);
				$data[$key]=$filename;
			}
		}
			if(!isset($data["user_id"])){
				$data["user_id"]=Auth::user()->id;
			}
			if(!isset($data['group_id'])){
				$data["group_id"]=Auth::user()->group_id;
			}
			
			$model::create($data);
			$getParams=  Session::has($this->name.'_filter') 
		? ('?'.http_build_query(Session::get($this->name.'_filter'))) : "";
			return Redirect::to('admin/'.Request::segment(2).$getParams);
			}catch(Exception $e){
				return Redirect::back()->withErrors(array($e->getMessage()))->withInput();
			}

}


	/**
	 * Update the specified category in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{

		$category = Category::findOrFail($id);
		$data = Input::all();

		Category::$rules['code'] = 'required|unique_with:categories,code,type,'.$category->id;
		$validator = Validator::make($data = Input::all(), Category::$rules);
		if(!isset($data['trusted'])){
			$data['trusted']=false;
		}
		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		if(Input::get('icon')){
		if (!File::exists(public_path()."/img/categories/".Input::get('icon')))
			{
			    copy(public_path()."/img/icons/".Input::get('icon'),public_path()."/img/categories/".Input::get('icon'));
			}
			$data["image"]=Input::get('icon');
		}else{

			$destinationPath= public_path()."/img/".Request::segment(2);
			foreach ($_FILES as $key => $file) {
				if(! Input::hasFile($key)) {
					unset($data[$key]);
					continue;
				}
				$file= Input::file($key);
				if(!$category->{$key}){
					$filename=str_random(6) . '_' . $file->getClientOriginalName();
			    	$file->move($destinationPath, $filename);
					$data[$key]=$filename;
				}else{
					//$filename=$category->{$key};
					$filename=str_random(6) . '_' . $file->getClientOriginalName();
			    	$file->move($destinationPath, $filename);
					$data[$key]=$filename;
				}
			}
		}

		$category->update($data);

		$getParams=  Session::has($this->name.'_filter') 
		? ('?'.http_build_query(Session::get($this->name.'_filter'))) : "";
			return Redirect::to('admin/'.Request::segment(2).$getParams);
	}

    public function export(){


		$filters=new TableFilter($this->table);
		$filters->table="categories";
		$filters->addFilter("type",TableFilter::SELECT);
		$filters->addFilter("title",TableFilter::TEXT);
		$filters->addFilter("code",TableFilter::TEXT);
		$filters->addFilter("tag",TableFilter::TEXT);
		$filters->addFilter("description",TableFilter::TEXT);
		$filters->addFilter("group_id",TableFilter::SELECT);
		$filters->addFilter("deleted",TableFilter::DELETED);
		$filters->sort();
		/*if($filter["status"]){

		}*/

		$data=$filters->getResult()->select(DB::raw('categories.*, groups.name as group_name'))->get();
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

		$user_id="";
		if(Auth::user()->can("manage_maps")){
			$group_id=Input::get('group_id');
		}else{
			$group_id=Auth::user()->id;
		}
		
 		$validator = Validator::make( 
 			array('group_id'=>$group_id),
 			array('group_id'=>'required'),
 			array('group_id'=>'User is required'
 		));
		if(!$validator->passes()){
			return Redirect::back()->withErrors($validator)->withInput();
		}
		$overwrite=Input::has('overwrite');

		$requiredColumns=array('code','title','tag');
		foreach ($data as $key => &$row) {

			if($key==0){
				foreach ($requiredColumns as $k => $column) {
					if(!isset($row[$column])){
						Session::flash('error',"The $column column is required in CSV file");
						return Redirect::back();	
					}
				}	
			}
			if(strlen($row['code'])!=3){
				Session::flash('error',"The code column must be 3 characters");
				return Redirect::back();	
			}

			$row["group_id"]=$group_id;
			if($overwrite){
				$cat=Category::where('code','=',$row['code'])->where('type','=',$row['type'])
				->first();
				if($cat){
					$cat->fill($row);
					$cat->save();	
					unset($data[$key]);
				}
			}

			$image=$this->downloadImage($row['image'],public_path()."/img/categories/");
			if($image){
				$row['image']=$image;
			}
		

		}
		if(!Input::has('overwrite')){
			$q=Category::where(function($q) use ($data){
				foreach ($data as $key => $row) {
					$q->orWhere('code','=',$row['code'])
						->where('type','=',$row['type']);
				}
			});
			
			if($q->count()>0){
				Session::flash('error',"One or more codes are duplicates for the same type");
				return Redirect::back();
			}
		}	
		try{
			if(count($data)>0){
				Category::insert($data);
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
