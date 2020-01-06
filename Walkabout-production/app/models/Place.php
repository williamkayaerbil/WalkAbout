<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
class Place extends \Eloquent {
	use SoftDeletingTrait;
	// Add your validation rules here
	public static $rules = [
		'title' => 'required',
		'layer_id'=>'required',
		'lat'=>'required',
		'lng'=>'required',
		'category_id'=>'required',
		'facebook'=>'url',
		'twitter'=>'url',
		'instagram'=>'url',
		'picture'=>'mimes:jpeg,bmp,jpg,png'
	];
	public static $columns = ['id','title','description','permanent','start','end','layer_id','picture','facebook','twitter','instagram','location','lat','lng','website','verified','category_id','created_at','updated_at'];
	public $check_values=array('permanent');
	// Don't forget to fill this array
	protected $fillable = ['title','description','permanent','start','end','layer_id','picture','facebook','twitter','instagram','location','lat','lng','website','verified','category_id'];
		public function layer()
	{
		return $this->belongsTo('Layer')->withTrashed();;
	}
	public function events()
	{
		return $this->hasMany('Event');
	}
	public function category()
	{
		return $this->belongsTo('Category')->withTrashed();;
	}
	
	public function hasPermission(){
		return true;
		/*
		if(Auth::user()->hasRole("Admin")){
			return true;
		}else if(Auth::user()->can("manage_maps")){
			return $this->layer->map->user->group_id==Auth::user()->group_id;
		}else{
			return $this->layer->map->user->id==Auth::user()->id;
		}*/
	}

	public function getCleanTitleAttribute()
	{
	    return strip_tags($this->title);
	}

	public static function getBuilder(){
		  $table=Place::leftJoin('categories','categories.id','=','places.category_id')
				->leftJoin('layers','layers.id','=','places.layer_id')
				->leftJoin('maps','maps.id','=','layers.map_id')
				->leftJoin('events','events.place_id','=','places.id')
				->groupBy('places.id');

		if(Auth::user()->hasRole('Admin')){

		}else
		if(Auth::user()->hasRole('Account Admin')){
		    $table->with('layer')->leftJoin('users','users.id','=','maps.user_id')
				   ->whereIn('users.group_id',Auth::user()->getGroupIds());
		}else if(Auth::user()->can("manage_maps")){
			
		    $table->with('layer')->leftJoin('users','users.id','=','maps.user_id')
				   ->where('users.group_id','=',Auth::user()->group_id);
		}else{
			$table->with('layer')->where('maps.user_id','=',Auth::user()->id);
		}
		return $table;
	}
}