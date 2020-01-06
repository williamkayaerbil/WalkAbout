<?php

class Interaction extends \Eloquent {
	public $timestamps = false;
	protected $table='interactions';
	protected $fillable = ['UID','client_ip','map_id','latitude','longitude','object','object_id','action','time'];
}