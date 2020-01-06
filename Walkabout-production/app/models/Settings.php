<?php
class Setting extends Eloquent
{
	public $timestamps = false;
	protected $fillable = array('key');
	public static function get($key){
		$ret=self::firstOrCreate(array('key'=>$key));
		return $ret->value;
	}
	public static function set($key,$value){
		$set=self::firstOrNew(array('key'=>$key));
		$set->value=$value;
		$set->save();
	}
}