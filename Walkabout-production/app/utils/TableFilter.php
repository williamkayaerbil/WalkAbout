<?php 
class TableFilter{
	const TEXT=1;
	const SELECT=2;
	const DEPENDENT=3;
	const DELETED=4;
	private $filters=array();
	private $model;
	public $table="";
	private $customFilters=array();
	public function __construct($model){
		$this->model=$model;
	}
	public function addFilter($name,$type){
		$this->filters[$name]=$type;
	}
	public function addCustomFilter($filter){
		if(is_callable($filter)){
			$this->customFilters[]=$filter;
		}
	}
	public function sort(){
		if(!Input::has('orderBy')) return;
		$orders=Input::get('orderBy');
		foreach ($orders as $key => $order) {
			if($order=="asc"||$order=="desc"){
				$this->model->orderBy($key,$order);	
			}
		}
	}
	public function getResult($debug=false){
		if(!Input::has('filter')) return $this->model;
		$f=Input::get('filter');
		foreach($this->filters as $key=>$filter){
			if(isset($f[$key]) && $f[$key]){
				$value=$f[$key];
				if($filter==self::TEXT){
					$this->model->where($this->table.".".$key,'like','%'.$value.'%');
				}else if($filter == self::SELECT){
					$this->model->where($this->table.".".$key,'=',$value);
				}else if($filter==self::DELETED){
					$this->model->onlyTrashed();
				}
			}
		}
		foreach ($this->customFilters as $key => $filter) {
			$filter($this->model);
		}
		if($debug){
			$this->model->get();
			var_dump( DB::getQueryLog());
			exit;
		}
		return $this->model;
	}

}