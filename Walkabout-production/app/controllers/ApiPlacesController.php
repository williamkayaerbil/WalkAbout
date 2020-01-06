<?php

class ApiPlacesController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{

		$max=100;
		$itemsPerPage=20;
		if(Input::has('max')){
			if($max>=Input::get('max')){
				$itemsPerPage=Input::get('max');
			}else{
				$itemsPerPage = $max;
			}
		}

		$query= Place::getBuilder();
		if(Input::has('sort')){
			$fields = strtolower(Input::get('sort'));
			$fields = explode(',',$fields);
			foreach ($fields as $key => $field) {
				$order = str_replace('-','', $field);
				$type = strlen($order)< strlen($field)?"DESC":'ASC';
				$query->orderBy($order,$type);
			}

		}

		foreach(Place::$columns as $column){  //search by columns
			if(Input::has($column)){
				$query->where($column,'=',Input::get($column));
			}
		}
		if(Input::has('layer_id')){
			$itemsPerPage=	$query->count();
		}
		if(Input::has('q')){  //full text search
			$query->where(function($q){
				$q->where(DB::raw('upper(title)'),'LIKE','%'.strtoupper(Input::get('q')).'%')
				->orWhere(DB::raw('upper(description)'),'LIKE','%'.strtoupper(Input::get('q')).'%');
			});
		}

		/*if(Input::has('map')){
			$query->with('layers.map');
		}
		if(Input::has('events')){
			$query->with('events');
		}*/
		$pagination = $query->select('places.*')->paginate($itemsPerPage);

		$next='';
		$prev='';

		if($pagination->getCurrentPage()<$pagination->getLastPage()  && !Input::has('layer_id')){
			$next = url('api/v1/places?page='.($pagination->getCurrentPage()+1));
		}
		if($pagination->getCurrentPage()>1){
			$prev = url('api/v1/places?page='.($pagination->getCurrentPage()-1));
		}
		$pagination = array('prev'=>$prev) + array('next'=>$next) +  $pagination->toArray();

		foreach ($pagination['data'] as $key => &$item) {
			$item['picture'] = asset('img/places/'.$item['picture']);
			if(!Input::has('layer')){
				unset($item['layer']);
			}
		}
		return $pagination;

	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}
	private function response($code,$message){
		$error = $code>=400;

 		return Response::json(array(
 				'status'=>$code,
		        'error' => $error,
		        'data' =>  $message
		        ),$code);

	}
	public function readCSV(){

		$file=Input::file('file');
		$resource=fopen($file->getRealPath(),"r");
		$i=0;
		$columns=[];
		$data=[];
		$file=file_get_contents($file->getRealPath());

	    if(strpos($file,Input::get('delimiter',','))===FALSE){
				return $data;
		}

		while( ($row=fgetcsv($resource,0,Input::get('delimiter',',')))!==FALSE){

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

	public function import(){

		if(!Input::hasFile('file')){
			return $this->response(400,"CSV File is required");
		}
		$data=$this->readCSV();
		if(count($data)===0){
			return $this->response(400,"Invalid Format");
		}
		if(!Input::has('layer_id')){
			return $this->response(400,"Layer is required");
		}
		$layer_id=Input::get('layer_id');
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
						return $this->response(400,"The $column column is required in CSV file");
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
				return $this->response(400,"One or more titles are duplicates");
				return Redirect::back();
			}
		}
		try{
			if(count($data)>0){
				Place::insert($data);
			}

		}catch(Exception $e){
			if(isset($e->errorInfo[2])){
				return $this->response(400, $e->errorInfo[2]);
			}else{
				return $this->response(400,$e->getMessage());
			}

		}
		return $this->response(200, 'Data imported successfully');

	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{

		/*'	'title' => 'required',
		'layer_id'=>'required',
		'lat'=>'required',
		'lng'=>'required',
		'category_id'=>'required',
		'facebook'=>'url',
		'twitter'=>'url',
		'instagram'=>'url',
		'picture'=>'mimes:jpeg,bmp,jpg,png''*/
		$content = Request::getContent();
		$data = (array)json_decode($content);
		if(!$data){
			return Response::json(array(
					 'status' => 422,
					 'error'=>true,
					 'data' =>'Invalid json'
				 ),422);
		}
		header('Content-Type: application/json');

		$layer_id = $data['layer_id'];
		$layer = Layer::find($layer_id);
		if(!$layer) {

			return Response::json(array(
					 'status' => 422,
					 'error'=>true,
					 'data' =>"the layer does not exists"
				 ),422);
		}



		$validator = Validator::make($data,Place::$rules);
		if ($validator->fails())
		{
		   return Response::json(array(
		        'status' => 422,
		        'error'=>true,
		        'data' =>$validator->messages()
		        ),422
		    );
		}


		if(!$this->check_permissions($layer_id)){  // user must have permissions to save place into layer
			return Response::json(array(
	 				'status' => 422,
	 				'error'=>true,
	 				'data' =>'Forbidden'
	 				),422
	 		);
		}


		$place = Place::create($data);
		$url_resource = url('api/v1/places/'.$place->id);
		header('Location: '.$url_resource);

 		return Response::json(array(
 				'status'=>201,
		        'error' => false,
		        'data' =>  $place,
		        'url_resource'=>$url_resource
		        ),201
		    );
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$place = Place::find($id);
		if($place){
			return $place;
		}
		return Response::json(array(
			'status'=>404,
	        'error' => true,
	        'data' =>  "not found",
	        ),404
	    );
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}
	private function check_permissions($layer_id){

		$table = Layer::leftJoin('maps','maps.id','=','layers.map_id')
					->where('layers.id','=',$layer_id);
		if(Auth::user()->hasRole('Account Admin')){
				$table->leftJoin('users','users.id','=','maps.user_id')
					 ->whereIn('users.group_id',Auth::user()->getGroupIds());
		}else if(Auth::user()->can("manage_maps")){
				$table->leftJoin('users','users.id','=','maps.user_id')
					 ->where('users.group_id','=',Auth::user()->group_id);
		}else  if(!Auth::user()->hasRole('Admin')){
				$table->where('maps.user_id','=',Auth::user()->id);
		}
		return $table->count() > 0;
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$place = Place::find($id);

		if($place){
			if(!$this->check_permissions($place->layer_id)){  // user must have permissions to save place into layer
				return Response::json(array(
						'status' => 422,
						'error'=>true,
						'data' =>'Forbidden'
						),422
				);
			}
			$content = Request::getContent();
			$data = (array)json_decode($content);

			$place->update($data);

			return Response::json(array(
 				'status'=>200,
		        'error' => false,
		        'data' =>  $place,
		        ),200
		    );
		}

			return Response::json(array(
 				'status'=>404,
		        'error' => true,
		        'data' =>  "not found",
		        ),404
		    );
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$place = Place::find($id);

		if($place){
			if(!$this->check_permissions($place->layer_id)){  // user must have permissions to save place into layer
				return Response::json(array(
						'status' => 422,
						'error'=>true,
						'data' =>'Forbidden'
						),422
				);
			}
			$place->delete();

			return Response::json(array(
 				'status'=>200,
		        'error' => false,
		        'data' =>  '',
		        ),200
		    );
		}
	}


}
