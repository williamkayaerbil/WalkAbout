<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
class Event extends \Eloquent {
	use SoftDeletingTrait;
	// Add your validation rules here
	public static $rules = [
		'title' => 'required',
		'place_id'=>'required',
		'start'=>'required|date',
		'end'=>'required|date',
		'category_id'=>'required',
		'facebook'=>'url',
		'twitter'=>'url',
		'instagram'=>'url',
		'picture'=>'mimes:jpeg,bmp,jpg,png'
	];


	// Don't forget to fill this array
	protected $fillable = ['place_id','title','description','locale','picture','facebook','twitter','instagram','start','end','category_id'];

	public function place()
	{
		return $this->belongsTo('Place')->withTrashed();;
	}
	public function category()
	{
		return $this->belongsTo('Category')->withTrashed();;
	}

	public static function getBuilder(){
		$table=Event::with('place')->leftJoin('places','places.id','=','events.place_id')
			       ->leftJoin('layers','layers.id','=','places.layer_id')
				   ->leftJoin('maps','maps.id','=','layers.map_id')
				   ->leftJoin('categories','categories.id','=','events.category_id');

		if(Auth::user()->hasRole("Admin")){

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
		return $table;
	}

		public function hasPermission(){
			return true;
			/*if(Auth::user()->hasRole("Admin")){
				return true;
			}else if(Auth::user()->can("manage_maps")){
				return $this->place->layer->map->user->group_id==Auth::user()->group_id;
			}else{
				return $this->place->layer->map->user->id==Auth::user()->id;
			}*/
	}
	

}