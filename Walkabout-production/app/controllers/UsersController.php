<?php

class UsersController extends \BaseController {

	public $singular_name="User";
	
  	public function __construct(){
		parent::__construct();
		$this->table=User::with('group')->with('roles');
		if(Auth::user()->hasRole('Admin')){
			$this->groups=Group::all()->lists('name','id');
			$this->roles=Role::all()->lists('name','id');	
		}else{
			$this->groups=Auth::user()->groups()->select('groups.*')->lists('name','id');
			$this->roles=Role::where('name','!=','Admin')
			 	  ->where('name','!=','Account Admin')->lists('name','id');
			 	  $ids=array_keys($this->groups);

	       $this->table->whereIn('group_id',Auth::user()->getGroupIds())
	       			->where('id','!=',Auth::user()->id);
		}

		
	}
	/**
	 * Display a listing of users
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
		$filters->addFilter("email",TableFilter::TEXT);
		$filters->addFilter("group_id",TableFilter::SELECT);
		$filters->addFilter("deleted",TableFilter::DELETED);
		$filters->addCustomFilter(function($query){ //Filter by role
			if(!isset(Input::get('filter')['role_id'])) return;
			$rol=Input::get('filter')['role_id'];
			if($rol){
				$query->leftJoin('assigned_roles','assigned_roles.user_id','=','users.id')	
					->where('role_id','=',$rol);
			}
		}); 

		$data=$filters->getResult()->select(DB::raw("users.*"))->paginate($this->pagination);
		$columns=array(
		
			array(
				"name"=>'name',
				"text"=>'Name',
				"attributes"=>'style="width:30px"'
			),
			array(
				"name"=>'email',
				"text"=>'Email'
			),
			array(
				"name"=>'roles',
				"text"=>'Role',
				"value"=>function($roles){
					return isset($roles[0])?$roles[0]->name:"";
				}
			),
			array(
				"name"=>'group',
				"text"=>'Group',
				"value"=>function($group){
					if(!$group) return "";
					if($group->trashed()){
						return "<span class='text-danger'>".$group->name."</span>";
					}
					return '<a href="'.url('admin/groups/'.$group->id.'/edit').'">'.$group->name.'</a>';
				}
			),
		);
			
		return View::make('users.index')
		->with('data',$data)
		->with('groups',$this->groups)
		->with('roles',$this->roles)
		->with('columns',$columns);
	}

	public function create(){
		return parent::create()
		->with('groups',$this->groups)
		->with('groups_account',Group::where('user_id','=',0)->orWhere('user_id','=',null)->lists('name','id'))
		->with('roles',$this->roles);
	}
	public function edit($id){
	
		
		return parent::edit($id)
		->with('groups',$this->groups)
		->with('groups_account',Group::where('user_id','=',0)->orWhere('user_id','=',null)->orWhere('user_id','=',$id)->lists('name','id'))
		->with('roles',$this->roles);
	}
public function save($model,$data){
			
			$destinationPath= public_path()."/img/".Request::segment(2);
			$uploadSuccess=true;

		
			try{
			foreach ($_FILES as $key => $file) {
				if(! Input::hasFile($key)) continue;
				$file= Input::file($key);
				$filename=str_random(6) . '_' . $file->getClientOriginalName();
			    $file->move($destinationPath, $filename);
				$data[$key]=$filename;
			}
			if(empty($data['password'])){
				throw new Exception("Password is required");
			}
			$data["type"]=2; //Manual user


			DB::transaction(function() use($data)
			{
				$u=User::create($data);
				$u->roles()->detach();
				$rol=Role::find($data['role_id']);
				if($rol){
					$u->attachRole($rol);	
				}
				if(intval($data['role_id'])===4){
					if(!isset($data['groups'])){
						$data['groups']=array(-1);
					}
					$groups=Group::whereIn('id',$data['groups'])->get();
					foreach($groups as $group){
						$group->user_id=$u->id;
						$group->save();
					}
				//$u->groups()->sync($data['groups']);
				} 
			});
			
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

		$user = $this->table->where(Request::segment(2).'.id','=',$id)->select(Request::segment(2).'.*')->first();
		if(!$user){
			App::abort(404);
		}

		$data = Input::all();
		$user->roles()->detach();
			$rol=Role::find($data['role_id']);
			if($rol){
				$user->attachRole($rol);
			}	
			$groups=$user->groups;

       
			foreach($groups as $group){
				$group->user_id=null;
				$group->save();
			}
			if(!isset($data['groups'])){
				$data['groups']=array(-1);
			}
			
			$groups=Group::whereIn('id',$data['groups'])->get();
			foreach($groups as $group){
				$group->user_id=$user->id;
				$group->save();
			}
			
			//if(intval($data['role_id'])===4){
			//	$user->groups()->sync($data['groups']);
			//} 
		
		if(empty($data['password'])){
			 unset($data['password']);
		}
		//var_dump($data); exit;
		User::$rules['email'] = 'required|unique:users,email,' . $user->id;
		$validator = Validator::make($data, User::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}
		$user->update($data);

		  $getParams=  Session::has($this->name.'_filter') 
		? ('?'.http_build_query(Session::get($this->name.'_filter'))) : "";
			return Redirect::to('admin/'.Request::segment(2).$getParams);
	}



    public function export(){

    	$filters=new TableFilter($this->table);
		$filters->addFilter("name",TableFilter::TEXT);
		$filters->addFilter("email",TableFilter::TEXT);
		$filters->addFilter("group_id",TableFilter::SELECT);
		$filters->addFilter("deleted",TableFilter::DELETED);
		$filters->addCustomFilter(function($query){ //Filter by role
			if(!isset(Input::get('filter')['role_id'])) return;
			$rol=Input::get('filter')['role_id'];
			if($rol){
				$query->leftJoin('assigned_roles','assigned_roles.user_id','=','users.id')	
					->where('role_id','=',$rol);
			}
		}); 

		$data=$filters->getResult()->select(DB::raw("users.*"))->get();
	
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

		$group_id=Input::get('group_id');
		
 		$validator = Validator::make( 
 			array('group_id'=>$group_id),
 			array('group_id'=>'required'),
 			array('group_id'=>'Group is required'
 		));
		if(!$validator->passes()){
			return Redirect::back()->withErrors($validator)->withInput();
		}
		$overwrite=Input::has('overwrite');
		$requiredColumns=array(
			'role','email','password','name'
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

			$row["group_id"]=$group_id;
			if($overwrite){
				$user=User::where('email','=',$row['email'])->first();
				if($user){
					$user->fill($row);
					$rol=Role::find($row['role']);
					if($rol){
						$user->roles()->detach();
						$user->attachRole($rol);	
					}

					$user->save();	
					unset($data[$key]);
				}
			}
		}

		if(!Input::has('overwrite')){
			$q=User::where(function($q) use ($data){
				foreach ($data as $key => $row) {
					$q->orWhere('email','=',$row['email']);
				}
			});
			
			if($q->count()>0){
				Session::flash('error',"One or more emails are duplicates");
				return Redirect::back();
			}
		}	
		try{
			if(count($data)>0){
				foreach($data as &$r){
					$user=User::create($r);
					$rol=Role::find($r['role']);
					if($rol){
						$user->attachRole($rol);	
					}
				}
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
