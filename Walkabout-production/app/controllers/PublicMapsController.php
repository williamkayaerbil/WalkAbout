<?php
class PublicMapsController extends Controller {


public function index($hash){
	/*  if (!isset($_COOKIE['visited'])) { 
        //setcookie ('visited', 'yes', time() + 3600*24*90); // 3 months

        return View::make('first');
    }*/
	return View::make('maps')->with('hash',$hash);
}

protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
		
			$this->layout = View::make($this->layout);
			
		}
	}
}