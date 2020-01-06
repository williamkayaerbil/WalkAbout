<?php
class SettingsController extends \Controller{

   public function index(){

   		return View::make('settings.index');
   }

   public function store(){
   	     $rules=[
   	     	'name' => 'required',
			'email'=>'required|unique:users,email,' . Auth::user()->id,
			'password'=>''
   	     ];
   	     $password=Input::get('password');
   	     var_dump($password);
   	     if($password!=''){
   	     	$rules['password']='confirmed|min:8';
   	     }
   	     
		$validator = Validator::make($data = Input::all(),$rules);
		if(Input::get('password')==""){
			unset($data['password']);
		}

		if ($validator->fails())
		{
			var_dump($validator);
			return Redirect::back()->withErrors($validator)->withInput();
		}
		Auth::user()->fill($data);
		Auth::user()->save();
		return Redirect::back();
   }

   public function info(){
   		return View::make('settings.info');
   }
   public function expiration(){
      return View::make('settings.expiration');
   }

}