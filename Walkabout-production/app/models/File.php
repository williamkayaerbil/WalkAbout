<?php

class File extends \Eloquent {
	protected $table='files';
	protected $fillable = ['name','url','user_id','group_id'];
}