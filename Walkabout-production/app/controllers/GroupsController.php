<?php

class GroupsController extends \BaseController {

	public $singular_name="Group";
    public function __construct(){
    	parent::__construct();
    	if(Auth::user()->hasRole('Admin')){
			$this->table=Group::with('users');
		}else{
			$this->table=Auth::user()->groups()->select('groups.*');
		}
    }

	/**
	 * Display a listing of groups
	 *
	 * @return Response
	 */
	public function index()
	{
		if(!Input::has('filter')){
	    	Session::forget($this->name.'_filter');
	    }

		$filters=new TableFilter($this->table);
		$filters->addFilter("name",TableFilter::TEXT);
		$filters->addFilter("description",TableFilter::TEXT);
		$filters->addFilter("deleted",TableFilter::DELETED);
		$data=$filters->getResult()->paginate($this->pagination);
		$columns=array(
		
			array(
				"name"=>'name',
				"text"=>'Name',
				"attributes"=>'style="width:30px"'
			),
			array(
				"name"=>'description',
				"text"=>'Description'
			),
			array(
				"name"=>'users',
				"text"=>'Users',
				"value"=>function($users){
					if(($count=count($users))>0){
					$user=$users[0];
					return '<a href="'.url('admin/users?filter[name]=&filter[email]=&filter[group_id]='.$user->group->id.'&filter[role_id]=').'" title="Navigate to users">'.$count.'</a>';
					}
					return "0";
					
				}
			),
		);
			
		return View::make('groups.index')
		->with('data',$data)
		->with('columns',$columns);
	}

		public function save($model,$data){

			$destinationPath= public_path()."/img/".Request::segment(2);
			$uploadSuccess=true;
			try{
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
			if(!isset($data["user_id"])){
				$data["user_id"]=Auth::user()->id;
			}
			if(!isset($data['group_id'])){
				$data["group_id"]=Auth::user()->group_id;
			}

			$group=$model::create($data);
			if(Auth::user()->hasRole('Account Admin')){
				Auth::user()->groups()->save($group);
			}
			$getParams=  Session::has($this->name.'_filter')
		? ('?'.http_build_query(Session::get($this->name.'_filter'))) : "";
			return Redirect::to('admin/'.Request::segment(2).$getParams);
			}catch(Exception $e){
				return Redirect::back()->withErrors(array($e->getMessage()))->withInput();
			}

		} 


    public function export(){
    	$filters=new TableFilter($this->table);
		$filters->addFilter("name",TableFilter::TEXT);
		$filters->addFilter("description",TableFilter::TEXT);
		$filters->addFilter("deleted",TableFilter::DELETED);
		$data=$filters->getResult()->get();
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
		$overwrite=Input::has('overwrite');
		$requiredColumns=array('name');
		foreach ($data as $key => &$row) {

			if($key==0){
				foreach ($requiredColumns as $k => $column) {
					if(!isset($row[$column])){
						Session::flash('error',"The $column column is required in CSV file");
						return Redirect::back();	
					}
				}	
			}
		
			if($overwrite){
				$group=Group::where('name','=',$row['name'])->first();
				if($group){
					$group->fill($row);
					$group->save();	
					unset($data[$key]);
				}
			}


		}
		if(!Input::has('overwrite')){
			$q=Group::where(function($q) use ($data){
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
				Group::insert($data);
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
