<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
class Group extends \Eloquent {
	use SoftDeletingTrait;
	// Add your validation rules here
	public static $rules = [
		'name' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = ['name','description'];
	public function users()
	{
		return $this->hasMany('User')->withTrashed();
	}

	public function categories()
	{
		return $this->hasMany('Category');
	}
	public function user(){
		return $this->belongsTo('User');
	}
	/*public function groups(){
		return $this->belongsToMany('Group')->withTrashed();
	}*/


}