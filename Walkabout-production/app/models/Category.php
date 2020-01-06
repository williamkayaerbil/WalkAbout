<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
class Category extends \Eloquent {

  	use SoftDeletingTrait;
	// Add your validation rules here
	public static $rules = array(
		'title' => 'required',
		'tag' => 'required',
		'code'=>'required|unique_with:categories,code,type',
		'image'=>'mimes:png,jpeg,bmp'
	);
	public $check_values=array('trusted');
	protected $table='categories';
	// Don't forget to fill this array
	protected $fillable = ['code','group_id','title','tag','description','image','trusted','type','color'];
	public function group()
	{
		return $this->belongsTo('Group')->withTrashed();
	}
	public static function getCategories($type=1){
		$categories=array();
		if(Auth::user()->hasRole("Admin")){
			$categories=Category::where('type','=',$type);
		}else if(Auth::user()->hasRole('Account Admin')){
			$categories=Category::whereIn('group_id',Auth::user()->getGroupIds())
				  				   ->where('type','=',$type);
		}else if(Auth::user()->can("manage_maps")){
			$categories=Category::where('group_id','=',Auth::user()->group_id)
				  				   ->where('type','=',$type);
		}else{
			$categories=Category::where('group_id','=',Auth::user()->group_id)
				->where('type','=',$type);
		}
		$categories->orderBy('title','asc');
		return $categories;
	}

	public static function getBuilder(){
		$table=array();
		if(Auth::user()->hasRole("Admin")){
			$table=Category::with('group');
		}else if(Auth::user()->hasRole("Account Admin")){
			
			$table=Category::whereIn('categories.group_id',Auth::user()->getGroupIds());
		}
		else{
			$table=Category::where('categories.group_id','=',Auth::user()->group_id);
		}	
		$table->leftJoin('groups','groups.id','=','categories.group_id');

		return $table;
	}

}