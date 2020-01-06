<?php

class LoginController extends BaseController {

	protected function index()
	{
		return View::make("login.index");
	}
	public function logon()
	{
            
 		if( Auth::attempt( array('email' => Input::get('email'), 'password' => Input::get('password')) ))
 		{       

            $user=Auth::user();
            if($user->type==2){ //Paypal user
            	$agreement=PaymentsController::getAgreement($user->paypal_id);

            	$state=$agreement->state;
            	
                if($state==PaymentsController::$stateActive){
                	   return Redirect::to('/admin');
                }

                if($state==PaymentsController::$statePending){
                	Auth::logout();
                	return Redirect::to('/login')->with('message', 'Your payment has not been accepted yet');
                }

                if($state==PaymentsController::$stateCancelled){

                	$lastPayment=$agreement->agreement_details->last_payment_date;
                	$paymentTime=strtotime($lastPayment);
                	$expirationTime=strtotime($lastPayment." + 1".strtoupper($agreement->plan->payment_definitions[0]->frequency));
                	$now=strtotime('now');
     

                	if($now>=$expirationTime ){
                		$user=Auth::user();
                		Session::put('user_id',$user->id);
                		Auth::logout();
                		$paymentsController =new PaymentsController();
                		$urlRenew=$paymentsController->getUrlRenew($user);
                		return Redirect::to('/login')->with('message', 'Your account is expired. <a style="text-decoration:underline" href="'.$urlRenew.'">Renew now</a>');
                	}
                }


            }
            
            return Redirect::to('/admin');
 		}
 		else{
                        
 			return Redirect::to('/login')->with('message', 'User or password incorrect');
 		}
	}

}
