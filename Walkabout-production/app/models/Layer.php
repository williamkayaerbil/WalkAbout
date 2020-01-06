<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
class Layer extends \Eloquent {
	use SoftDeletingTrait;
	// Add your validation rules here
	public static $rules = [
		'name' => 'required',
		'map_id'=>'required'
	];

	// Don't forget to fill this array
	protected $fillable = ['name','description','map_id','kml_layer','geojson_layer',];
	public function map()
	{
		return $this->belongsTo('Map')->withTrashed();;
	}
	public function places()
	{
		return $this->hasMany('Place');
	}
	public function hasPermission(){
		return true;
		/*if(Auth::user()->hasRole("Admin")){
			return true;
		}else if(Auth::user()->can("manage_maps")){
			return $this->map->user->group_id==Auth::user()->group_id;
		}else{
			return $this->map->user->id==Auth::user()->id;
		}*/
	}


	public static function getBuilder(){
		$table=Layer::with('map')->leftJoin('maps','maps.id','=','layers.map_id')
		->leftJoin('places','places.layer_id','=','layers.id')
		->groupBy('layers.id');
		
		if(Auth::user()->hasRole('Admin')){

		}else
		if(Auth::user()->hasRole("Account Admin")){
			$table->leftJoin('users','users.id','=','maps.user_id')
					->whereIn('users.group_id',Auth::user()->getGroupIds());
		}else if(Auth::user()->can("manage_maps")){
			$table->leftJoin('users','users.id','=','maps.user_id')
					->where('users.group_id','=',Auth::user()->group_id);
		}else{
			$table->where('maps.user_id','=',Auth::user()->id);
		}
		$table->select("layers.*");




		return $table;
	}
}