<?php

class BaseController extends Controller {
	protected $table=null;
	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	public $singular_name="";
	public $attributes=array();
	public $model="";
	public $name="";
	protected $pagination=20;

	public function __construct(){

		View::share('singular_name', $this->singular_name);
		View::share('attributes',$this->attributes);
		$this->model=$this->singular_name;
		$this->name=Request::segment(2);
		if(Input::has('filter')){
		 	Session::put($this->name.'_filter',$_GET);
		 }

	}
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{

			$this->layout = View::make($this->layout);

		}
	}

	protected function normalizeDate($date){
		if(strpos($date, "/")!==FALSE){
			$temp=explode(" ",$date);
			$d=explode("/", $temp[0]);
			return $d[2]."-".$d[1]."-".$d[0]." ".$temp[1];
		}
		return $date;
	}


	/**
	 * Show the form for creating a new category
	 *
	 * @return Response
	 */
	public function create()
	{
		$layers=array();
		$places=array();
	   if(Session::has('_old_input')){
		   	 $input=Session::get('_old_input');
		   	 if(isset($input['map_id']) && !empty($input['map_id'])){
		   	 	$layers=ApiController::getLayers($input['map_id'])->lists('name','id');

		   	 }
		   	 if(isset($input['layer_id']) && !empty($input['layer_id'])){
		   	 	$places=ApiController::getPlaces($input['layer_id'])->lists('title','id');;
		   	 }
	   	 }

		$m=$this->model;
		$name=$this->name;
		return View::make($name.'.create')
		->with('model',new $m)
		->with('route','admin.'.$name.'.store')
		->with('method','POST')
		->with('places',$places)
		->with('layers',$layers);
	}

		/**
		 * Store a newly created category in storage.
		 *
		 * @return Response
		 */

		public function beforeSave(){

		}
		public function store()
		{
			$this->beforeSave();
			$singular_name=$this->singular_name;
			$validator = Validator::make($data = Input::all(), $singular_name::$rules);
			if ($validator->fails())
			{
				return Redirect::back()->withErrors($validator)->withInput();
			}
			return $this->save($singular_name,$data);
		}
		public function delete(){
			if(Input::get('delete')){
				$m=$this->model;
		    	$m::whereIn('id',Input::get('delete'))->delete();
			}
			return Redirect::back();
		}

		public function edit($id)
		{
			$name=$this->model;
			
			$model = $this->table->where(Request::segment(2).'.id','=',$id)->select(Request::segment(2).'.*')->first();
			
			//var_dump($model->toArray());exit;
			if($model==null) App::abort(404);
			if(method_exists ($model,'hasPermission')){

				/*if(!$model->hasPermission()){
					App::abort(404);
				}*/
			}
			//var_dump($model->map_id);exit;
			return View::make($this->name.'.create')
			->with('model',$model)
			->with('route',array('admin.'.strtolower($this->name).'.update',$model->id))
			->with('method','PUT')
			->with('name',$model->title?$model->title:$model->name);
		}

		public function save($model,$data){

			$destinationPath= public_path()."/img/".Request::segment(2);
			$uploadSuccess=true;
			//$destinationPath= public_path()."/img/".Request::segment(2);
			//$uploadSuccess=true;
			try{
				/*
			foreach ($_FILES as $key => $file) {
				if(! Input::hasFile($key)) {
					unset($data[$key]);
					continue;
				}
				$file= Input::file($key);
				
				$filename=str_random(6) . '_' . $file->getClientOriginalName();
			    $file->move($destinationPath, $filename);
				$data[$key]=$filename;

				$name=$file->getClientOriginalName();
				$i = 1;
				$actual_name = pathinfo($name,PATHINFO_FILENAME);
				$original_name = $actual_name;
				$extension = pathinfo($name, PATHINFO_EXTENSION);

				
				$userPath=public_path().DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'user_'.Auth::user()->id.DIRECTORY_SEPARATOR;

				$thumbnails=$userPath.'thumbnails/';
				$media=$userPath.'media/';
				if (!file_exists($userPath)) {
	    			mkdir($userPath, 0777);
	    		}
	    		if(!file_exists($thumbnails)){
	    			mkdir($thumbnails, 0777);
	    		}
	    		if(!file_exists($media)){
	    			mkdir($media, 0777);
	    		}
	    		$newFile=($media.$actual_name.'.'.$extension);
				while(file_exists($newFile)){
				    $actual_name = (string)$original_name.$i;
				    $name = $actual_name.".".$extension;
				    $newFile=$media.$name;
				    $i++;		
				}

				if(copy($destinationPath.'/'.$filename,$newFile)){
					$media=new File(array('name'=>$name,'url'=>''));

					 Image::make($newFile)
			          ->resize(100,100)
			          ->save($thumbnails.$name);
				}
			}*/
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

			$model::create($data);
			
			$getParams=  Session::has($this->name.'_filter')
		? ('?'.http_build_query(Session::get($this->name.'_filter'))) : "";
			return Redirect::to('admin/'.Request::segment(2).$getParams);
			}catch(Exception $e){
				return Redirect::back()->withErrors(array($e->getMessage()))->withInput();
			}

		}

	/**
	 * Generic update method
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$model=$this->model;
		$item = $this->table->where(Request::segment(2).'.id',$id)->select(Request::segment(2).'.*')->first();
		if(!$item){
			App:abort(404);
		}
		/*if(method_exists ($model,'hasPermission')){
			if(!$item->hasPermission()){
					App::abort(404);
			}
		}*/
		$validator = Validator::make($data = Input::all(), $model::$rules);
		if(isset($item->check_values)){
			foreach($item->check_values as $value){
				if(!isset($data[$value])){
					$data[$value]=false;
				}
			}

		}
		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}


		$destinationPath= public_path()."/img/".Request::segment(2);
		foreach ($_FILES as $key => $file) {
			if(! Input::hasFile($key)) {
				unset($data[$key]);
				continue;
			}
			$file= Input::file($key);
			if(!$item->{$key}){
				$filename=str_random(6) . '_' . $file->getClientOriginalName();
		    	$file->move($destinationPath, $filename);
				$data[$key]=$filename;
			}else{
				$filename=$item->{$key};
		    	$file->move($destinationPath, $filename);
				unset($data[$key]);
			}
		}
		/*foreach ($_FILES as $key => $file) {
			if(! Input::hasFile($key)) {
				unset($data[$key]);
				continue;
			}
			$file= Input::file($key);



			$filename=str_random(6) . '_' . $file->getClientOriginalName();
	    	$file->move($destinationPath, $filename);
			$data[$key]=$filename;



			if(!$item->{$key}){
				$filename=str_random(6) . '_' . $file->getClientOriginalName();
		    	$file->move($destinationPath, $filename);
				$data[$key]=$filename;
			}else{
				$filename=$item->{$key};
		    	$file->move($destinationPath, $filename);
				unset($data[$key]);
			}
 


		}*/

		$item->update($data);

	  $getParams=  Session::has($this->name.'_filter')
		? ('?'.http_build_query(Session::get($this->name.'_filter'))) : "";
			return Redirect::to('admin/'.Request::segment(2).$getParams);
	}
	public function restore($id){
		if($this->singular_name=="Map" && Auth::user()->getAvailableMaps()=="0"){
			return Redirect::to('admin/maps');
		}
		$model=$this->model;
		$item=$model::onlyTrashed()->find($id);
		if($item){
			$item->restore();
		}
		return Redirect::back();
	}
	 public function readCSV(){

		$file=Input::file('file');
		$resource=fopen($file->getRealPath(),"r");
		$i=0;
		$columns=[];
		$data=[];
		$file=file_get_contents($file->getRealPath());

	    if(strpos($file,Input::get('delimiter'))===FALSE){
				return $data;
		}
		
		while( ($row=fgetcsv($resource,0,Input::get('delimiter')))!==FALSE){

			if($i==0){
				$columns=$row;
			}else{


				$r=[];
				$valid=false;
				foreach ($row as $key => $value) {

					$r[$columns[$key]]=$value;
					if(!empty(trim($value))){
						$valid=true;
					}
				}
				unset($r[""]);
				if($valid){
					$data[]=$r;	
				}
			}
			$i++;
		}
		fclose($resource);
		
		return $data;
    }



	protected function downloadImage($url,$folder){
	    $pathinfo=pathinfo($url);

	    $validExtensions=['png','jpg','jepg','kmz','kml','json','geojson'];
	  
		if(filter_var($url,FILTER_VALIDATE_URL)===FALSE){

			return null;
		}
		
		if( isset($pathinfo['extension']) && !in_array($pathinfo['extension'], $validExtensions)){
			return null;
		}

		$filename=str_random(8).(isset($pathinfo['extension'])? '.'.$pathinfo['extension']:".png");

		$handle = fopen($url, "rb");
	  
		$fp = fopen($folder.$filename, 'w');
		$contents = '';
		while (!feof($handle)) {
		  $content = fread($handle, 8192);
		  fwrite($fp, $content);
		}
		fclose($handle);
		fclose($fp);
		return $filename;
	}
    public function writeCSV($data){
    	header("Content-Type: application/octet-stream");
        header( 'Content-Disposition: attachment;filename='.Request::segment(2).'.csv;');
        header("Pragma: no-cache");
		header("Expires: 0");
		header("Content-Transfer-Encoding: binary");
		$out = fopen('php://output', 'w');


		foreach ($data as $key => $row) {
			if(!is_array($row)){
				$arr=$row->toArray();	
			}
			
			if($key==0){
					$keys=array_keys($arr);
					fputcsv($out,$keys);
			}
			$values=array_values($arr);
			$filtered=array_filter($values,function($val){
				return !is_array($val);
			});

			fputcsv($out,$filtered);
		}

		fclose($out);
    }
}
