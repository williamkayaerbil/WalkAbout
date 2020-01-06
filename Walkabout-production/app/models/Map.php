<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
class Map extends \Eloquent {

 	use SoftDeletingTrait;
	// Add your validation rules here
	public static $rules = array(
		'name' => 'required',
		'header_logo'=>'mimes:jpeg,bmp,jpg,png',
		'menu_logo'=>'mimes:jpeg,bmp,jpg,png',
		'place_logo'=>'mimes:jpeg,bmp,jpg,png',
		'event_logo'=>'mimes:jpeg,bmp,jpg,png',
		'client_zoom'=>'integer'
		//'kml_layer'=>'mimes:kml'
	);


	// Don't forget to fill this array
	protected $fillable = ['user_id','hash','name','click_event','navigation_event','navigation_time','description','header_logo','menu_logo','place_logo','event_logo','header_logo_url','menu_logo_url','place_logo_url','event_logo_url','menu_options','center','zoom','zoom_ctrl_pos','maptype_ctrl_pos','pan_ctrl_pos','streetview_ctrl_pos','start_time','final_time','step','client_zoom'];
		public function user()
	{
		return $this->belongsTo('User')->withTrashed();
	}
	public function layers()
	{
		return $this->hasMany('Layer');
	}

	public static function boot()
    {
        parent::boot();

        // Setup event bindings...
    }
	public function hasPermission(){
		return true;
	}


    public function map_places()
    {
        return $this->hasManyThrough('Place', 'Layer');
    }

    public static function getMaps(){
    	$maps=array();
    	if(Auth::user()->hasRole("Admin")){
			$maps= Map::all();
		}else if(Auth::user()->hasRole('Account Admin')){	
			$maps= Map::leftJoin('users','users.id','=','maps.user_id')
		           ->whereIn('users.group_id',Auth::user()->getGroupIds())->select('maps.*')->get();
		}else if(Auth::user()->can("manage_maps")){
			$maps= Map::leftJoin('users','users.id','=','maps.user_id')
												->where('users.group_id','=',Auth::user()->group_id)->select('maps.*')->get();
		}else{
			$maps= Auth::user()->maps;			
		}
		return $maps;
    }
    public static function getBuilder($user=null){
    	if($user==null){
    		$user=Auth::user();
    	}
    	$table=Map::with('user')->leftJoin('users','users.id','=','maps.user_id')
				->leftJoin('groups','users.group_id','=','groups.id')
				->leftJoin('layers','layers.map_id','=','maps.id')
				->groupBy('maps.id');
		if($user->hasRole('Admin')){
			//$table=Map::with('user');
		}else
		if($user->hasRole('Account Admin')){
			$table->whereIn('users.group_id',$user->getGroupIds());
		}else
		if($user->can("manage_maps")){
			$table->where('users.group_id','=',$user->group_id);				
		}else{
			$table->where('maps.user_id','=',$user->id);
		}
		return $table;
    }
}