<?php
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\Plan;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\Currency; 
use PayPal\Api\ChargeModel;
use PayPal\Api\Agreement;
use PayPal\Api\ShippingAddress;
use PayPal\Api\Patch;
use PayPal\Common\PayPalModel;
use PayPal\Api\PatchRequest;
use PayPal\Api\AgreementStateDescriptor;
require app_path().'/utils/simple_html_dom.php';
class PaymentsController extends Controller{
	private $urlSuccess="http://dev.citydataco.com/register-successful/";
	private $urlCancel="http://dev.citydataco.com/cancelled/";
	private $urlWs="http://dev.citydataco.com/wp-admin/admin-ajax.php?";
	private $returnUrl="";
	private $urlPaypal="";
	private $urlMap="";


	public  static $stateActive="Active";
	public  static $statePending="Pending";
	public  static $stateExpired="Expired";
	public  static $stateSuspend="Suspend";
	public  static $stateReactivated="Reactivated";
	public  static $stateCancelled="Cancelled";


	//Frecuency of plans WEEK, DAY, YEAR, MONTH

	public function getMessage($message){
		echo "<h2>".$message."<h2>";
		exit;
	}
	public function renew(){
		
		$user=User::find(Session::get('user_id'));
		
		if($user){
			$agreement=self::getPlanInfo(Input::get('token'));
    		$user->paypal_id=$agreement->getId();
    		$user->paypal_token=Input::get('token');
    		$user->save();
    		Session::flush();
    		header('Location: '.$this->urlSuccess);
		}	

    	$this->getMessage("Transaction error");

	}
	public function getUrlRenew($user){

		Session::put('user_id',$user->id);
		Session::save();
		
		$plan=explode("|",$user->plan_id);
		$plan=$this->wsPlan($plan[0],$plan[1]);
		if(!$plan){
			return Redirect::to('/login');
		}

		$price=intval(trim(str_replace("$", "",$plan->planprice)));
		$period=$plan->planperiod;
		$description=$plan->planfeatures;
		$name=$plan->planname;
	
		$description=str_replace('</li>',"</li>\n", $description);
		$description=str_replace('</br>',"</br>\n", $description);
		$description=str_replace('</b>',"</b>\n", $description);
		$description=strip_tags($description);

		$this->returnUrl=url('/renew');
		$this->urlCancel=url('/login');
		return $this->createAgreement($price,$period, $description,$name);


	}
	public function wsPlan($plan_id,$post_id){
		$plan=json_decode(file_get_contents($this->urlWs.http_build_query(array('post_id'=>$post_id,'plan_id'=>$plan_id,'action'=>'ajax_action'))));
		if(!$plan){
			echo "<h2>Invalid plan</h2>";
			exit;
		}
		return $plan;

	}
	public static function getAgreement($agreementId){
		$agreement=null;
		try{
			$agreement = \PayPal\Api\Agreement::get($agreementId, self::getContext()); 
		}catch(Exception $e){
			var_dump($e->getMessage());
			exit;
		}
		return $agreement;
	
	}
	public static function getPlanInfo($token){

		$agreement = new \PayPal\Api\Agreement();
    	try{
    		 $agreement->execute($token,self::getContext()); 
    	} 
    	catch (Exception $ex) { 
    		var_dump($ex->getMessage());
    		exit(1); 
    	}
    	try { 
    		$agreement = \PayPal\Api\Agreement::get($agreement->getId(), self::getContext()); 
    	} catch (Exception $ex) { 
    		var_dump($ex->getMessage());
    		exit(1); 
    	}
    	
    	return $agreement;
	}
	private function showMessage($message){
			echo "<h2>".$message."<h2>";
			exit;
	}
	public function index(){
		//if(Input::has('data')){
		//	Session::forget('urlMap');
		//	Session::forget('urlPaypal');
		//}

	    //var_dump(Session::all());
	    //exit;

		$this->returnUrl=url('payments-success');
		if(Session::has('urlMap')){
			return $this->getPreview(Session::get('urlMap'),Session::get('urlPaypal'));
		}

		set_time_limit(100);
		$user=null;
		if(!Input::has('data')){
			$this->showMessage("No data");
		}
		$data=json_decode(Input::get("data"));

		$plan=json_decode(file_get_contents($this->urlWs.http_build_query(array('post_id'=>$data->userInfo->plan_post_id,'plan_id'=>$data->userInfo->plan_id,'action'=>'ajax_action'))));
		if(!$plan){
			echo "<h2>Invalid plan</h2>";
			exit;
		}
		//var_dump($plan); exit;
		//echo "<pre>";
	    //var_dump($data);exit;
		//$data->userInfo->name="Test name";
		//$data->userInfo->email="testem111@gmail.com";
		//$data->userInfo->password="123";
		//$data->userInfo->planId=23;
        //$data->userInfo->plan_price=123;
		//$data->userInfo->plan_id;
		//$data->userInfo->plan_post_id;
		//var_dump($data->userInfo);exit;
		//file_get_contents($urlWs.http_build_query(array("post_id"=>$data->userInfo->plan_post_id,'plan_id'=>$data->userInfo->plan_id)))
		//Get Current Plan

		//$data->userInfo->price=10;
		///$data->userInfo->period="Month";
		/////////////////////////////////
		
		$url="";

	 
		DB::transaction(function() use ($data,$user,$plan,$url)
		{
			$group=new Group();
			$group->name=$data->userInfo->name;
			$group->save();

			if($user=User::where('email','=',$data->userInfo->email)->first()){
				if(!$user->paypal_token){
						$price=intval(trim(str_replace("$", "",$plan->planprice)));
						$period=$plan->planperiod;
						$description=$plan->planfeatures;
						$name=$plan->planname;
						$description=strip_tags($description);
						$user->plan_id=$plan->planid;
						$this->urlPaypal=$this->createAgreement($price,$period, $description,$name);
						$this->urlMap=url('map/'.$user->maps[0]->hash);
						Session::put('urlPaypal', $this->urlPaypal);
						Session::put('urlMap',$this->urlMap);
						Session::put('user_id',$user->id);
						Session::save();
						return;
				}else{
					echo "<h2>The user already exists</h2>";
					exit;
				}
			
			}

			$user=new User();
			$user->name=$data->userInfo->name;
			$user->password=$data->userInfo->password;
			$user->email=$data->userInfo->email;
			$user->group_id=$group->id;
			$user->plan_id=$plan->planid."|".$data->userInfo->plan_post_id;
			$user->quota=$plan->planmaps;
			$user->type=2;
			$user->save();


			$group->user_id=$user->id;
			$group->save();
			$rol=Role::find(4);
			$user->attachRole($rol);	
			

			$map=new Map();

			$map->name=$data->mapInfo->name;
			$map->description=$data->mapInfo->description;
			$map->center=$data->mapInfo->coordinates;
			$map->step=60;
			$map->menu_options=json_encode($data->mapInfo->menu);
			$map->zoom=$data->mapInfo->zoom;
			$map->user_id=$user->id;
			$map->hash=md5(($map->id."-".time()).rand(0,1000));
			$data->mapInfo->logo=isset($data->mapInfo->logo)?$data->mapInfo->logo:'';
			$map->header_logo =$this->saveImage($data->mapInfo->logo,'/map_logos');
			$map->save();



			$layer=new Layer();
			$layer->name=$data->mapInfo->name;
			$layer->map_id=$map->id;
			$layer->save();

			$catPlaces=$data->categories->places;
			$count=count($catPlaces);
			$catPlacesTemp=array();
			for($i=0;$i<$count;$i++ ){
				$cat=new Category();
				$cat->title=$catPlaces[$i]->name;
				$cat->group_id=$group->id;
				$cat->color=$catPlaces[$i]->color;
				$cat->type=2;
			    $cat->image=$this->saveImage($catPlaces[$i]->image,'/categories');
				if(!$cat->save()){
					echo "<h2>Error saving categories</h2>";
					exit;
				}
				$catPlacesTemp[]=$cat;
			}

		    $catEvents=$data->categories->events;
			$count=count($catEvents);
			$catEventsTemp=array();
			for($i=0;$i<$count;$i++ ){
				$cat=new Category();
				$cat->title=$catEvents[$i]->name;
				$cat->group_id=$group->id;
				$cat->color=$catEvents[$i]->color;
				$cat->type=1;
				 $cat->image=$this->saveImage($catPlaces[$i]->image,'/categories');
				if(!$cat->save()){
					echo "<h2>Error saving categories</h2>";
					exit;
				}
				
				$catEventsTemp[]=$cat;
			}

			$places=$data->places;
			for($i=0; $i<count($places); $i++){
				$place=new Place();
				$place->title=$places[$i]->name;
				$place->description=$places[$i]->description;
				$location=explode(",",$places[$i]->location);
				$place->lat=$location[0];
				$place->lng=$location[1];
				$place->permanent=true;

				/*var_dump($places[$i]->category_id);
				var_dump($catPlacesTemp);
				var_dump($catPlacesTemp[$places[$i]->category_id]);
				exit;*/
				$place->category_id=$catPlacesTemp[$places[$i]->category_id]->id;
				$place->picture=$this->saveImage($places[$i]->img,'/places');
				$place->layer_id=$layer->id;
				if(!$place->save()){
				      echo "<h2>Error saving places</h2>";
					  exit;
				}
				//var_dump($places[$i]->event);exit;
				if(isset($places[$i]->event)&&$places[$i]->event){
					$event=new Event();
					$event->place_id=$place->id;
					$event->title=$places[$i]->event->title;
					$event->start=$places[$i]->event->start;
					$event->end=$places[$i]->event->end;
					$event->facebook='';
					$event->twitter='';
					$event->instagram='';
					$event->category_id=$catEventsTemp[$places[$i]->event->category_id]->id;
					$event->picture=$this->saveImage($places[$i]->event->img,'/events');
					if(!$event->save()){
				      echo "<h2>Error saving events</h2>";
					  exit;
					}
					
				}

			}
			
			$price=intval(trim(str_replace("$", "",$plan->planprice)));
			$period=$plan->planperiod;
			$description=$plan->planfeatures;
			$name=$plan->planname;
		
			$description=str_replace('</li>',"</li>\n", $description);
			$description=str_replace('</br>',"</br>\n", $description);
			$description=str_replace('</b>',"</b>\n", $description);
			$description=strip_tags($description);
			$this->urlPaypal=$this->createAgreement($price,$period, $description,$name);
			$this->urlMap=url('map/'.$map->hash);
			Session::put('urlPaypal', $this->urlPaypal);
			Session::put('urlMap',$this->urlMap);
			Session::put('user_id',$user->id);
			Session::save();
			//exit;

    	});
	
		return $this->getPreview($this->urlMap,$this->urlPaypal);

	}
	private function getPreview($urlMap,$urlPaypal){

		$content=file_get_contents($urlMap);
		$banner=View::make('payments.banner')
			   ->with('urlPaypal',$urlPaypal)->render();

	    $bannerContent=str_get_html($banner);
	    $html = str_get_html($content);
	    $html->getElementById("content")->appendChild($bannerContent->getElementById('banner'));
	    echo $html; exit;
		return View::make('payments.preview')
			   
			   ->with('mapContent',$mapContent);
	}
	public function saveImage($base64,$subdir){
		$img=$this->saveBase64Image($base64,public_path()."/img/".$subdir);
		$path=pathinfo($img);
		if(isset($path['extension'])){
			return $path["filename"].'.'.$path['extension'];
		}
		return '';
	}
	private function isBase64($base64){
		return strpos($base64, "no-img.jpg")===FALSE && strpos($base64, 'http')===FALSE || $base64!="";
	}
	
    private function saveBase64Image($base64, $folder){
    	if($base64=="") return "";
    	if(!$this->isBase64($base64)) return "";

         $data = explode(',', $base64);
         
         $output_file=$folder."/".str_random(8).".png";

         while(file_exists($output_file)){
         	$output_file=tempnam($folder,str_random(5)).".png";
         }
         
         if(isset($data[1])){
    		$file = fopen($output_file, "wb"); 
        	 fwrite($file, base64_decode($data[1])); 
	         fclose($file); 
	         
	      
	   		 return $output_file; 

         }
         return "";
     
	
    }
    public function cancelRequest(){
    	var_dump(Input::all());
    }

    public function successRequest(){
    	var_dump( Session::all());
    	$user=User::where('id','=',Session::get('user_id'))->first();
    	if($user){
    		$agreement=self::getPlanInfo(Input::get('token'));
    		$user->paypal_id=$agreement->getId();
    		$user->paypal_token=Input::get('token');
    		$user->save();
    		Session::flush();
    		header('Location: '.$this->urlSuccess);
    	    exit;
    	}

    	$this->getMessage("Transaction error");
    	//var_dump($agreement->getId());exit;
    	//$user->paypal_token=Input::get('token');
    	//$user->save();
    
    	// 
    	
    }
	private function createPlan($price,$period,$description,$name){
		$plan =new Plan();
		$plan->setName($name) 
		     ->setDescription($description) 
		     ->setType('INFINITE');

		$paymentDefinition = new PaymentDefinition();
		$paymentDefinition->setName('Regular Payments') 
		                  ->setType('REGULAR') 
		                  ->setFrequency($period) 
		                  ->setFrequencyInterval("1") 
		                  ->setCycles("0") 
		                  ->setAmount(new Currency(array('value' => $price, 'currency' => 'USD')));

		/*$chargeModel = new ChargeModel(); 
		$chargeModel->setType('SHIPPING') 
		            ->setAmount(new Currency(array('value' => 10, 'currency' => 'USD'))); */

		//$paymentDefinition->setChargeModels(array($chargeModel)); 
		$merchantPreferences = new MerchantPreferences();
		$merchantPreferences->setReturnUrl($this->returnUrl)
		                    ->setCancelUrl($this->urlCancel)
		                    ->setAutoBillAmount("yes");
		                    //->setInitialFailAmountAction("CANCEL")
		                    //->setMaxFailAttempts("0")
		                    //->setSetupFee(new Currency(array('value' => 0, 'currency' => 'USD')));

		$plan->setPaymentDefinitions(array($paymentDefinition)); 
		$plan->setMerchantPreferences($merchantPreferences);
		$request = clone $plan;
		try { 
			$output = $plan->create($this->getApiContext()); 
		} catch (Exception $ex) { 
			var_dump($ex->getMessage());
			//ResultPrinter::printError("Created Plan", "Plan", null, $request, $ex); 
			exit(1); 
		}
	
	
		return $output;

	}
	public function updatePlan($createdPlan){
		try {
	    $patch = new Patch();

	    $value = new PayPalModel('{
		       "state":"ACTIVE"
		     }');

	    $patch->setOp('replace')
	        ->setPath('/')
	        ->setValue($value);
	    $patchRequest = new PatchRequest();
	    $patchRequest->addPatch($patch);

	    $createdPlan->update($patchRequest,$this->getApiContext());
	    return $createdPlan;
	    //$plan = Plan::get($createdPlan->getId(),$this->getApiContext());

	} catch (Exception $ex) {
	   	var_dump($ex->getMessage());
	    exit(1);
	}


	return $plan;

	}
	public function createAgreement($price,$period, $description,$name){

		
		$createdPlan=$this->createPlan($price,$period,$description,$name);
		$createdPlan=$this->updatePlan($createdPlan);
	
		$agreement = new Agreement(); 
		$agreement->setName($name) 
				  ->setDescription($description)
				 //->setStartDate('2015-06-17T9:45:04Z');
				  //->setStartDate('2015-06-16T9:45:04Z');
				  
				  //->setStartDate(date("Y-m-d\TH:i:s\Z"));
				  ->setStartDate(date("Y-m-d\T23:59:59\Z"));


		$plan = new Plan(); 
		$plan->setId($createdPlan->getId());
		$agreement->setPlan($plan);
		
		$payer = new Payer(); 
		$payer->setPaymentMethod('paypal'); 
		$payer->setStatus('VERIFIED');
		$agreement->setPayer($payer);


		$request = clone $agreement;

		try {

			$agreement = $agreement->create($this->getApiContext());	
			$approvalUrl = $agreement->getApprovalLink(); 
			
		} catch (Exception $ex) { 
			var_dump($ex->getMessage()); 
			exit(1); 
		} 

		return $approvalUrl;
		
	}
	private function createPayment(){
		$payer = new Payer(); 
		$payer->setPaymentMethod("paypal");
		$itemList=$this->getItems();
		$amount=$this->getAmount(20,"Walkabout access");
		$transaction=$this->getTransaction($amount,$itemList,'Walkabout maps');
	    $redirectUrls=$this->getRedirectUrls();
	    $payment=$this->getPayment($payer,$redirectUrls,$transaction);
		$request = clone $payment;
	    try { 
	        $payment->create($this->getApiContext());
	    } catch (Exception $ex) { 
	    	echo ("Created Payment Using PayPal. Please visit the URL to Approve.");

	    	var_dump($request);
	    	echo $ex->getMessage();
	    	exit(1); 
	    }


		var_dump($payment);
		
	}
	private function getPayment($payer,$redirectUrls,$transaction){
		$payment = new Payment(); 
		$payment->setIntent("sale") 
		->setPayer($payer) 
		->setRedirectUrls($redirectUrls) 
		->setTransactions(array($transaction));
		return $payment;
	}
	private function getRedirectUrls(){
	    $redirectUrls = new RedirectUrls();
	    $redirectUrls->setReturnUrl(url('/payments/accept')) 
	    ->setCancelUrl(url('payments/cancel'));
	    return $redirectUrls;
	}

	private function getTransaction($amount,$itemList,$description){
		$transaction = new Transaction(); 
		$transaction->setAmount($amount) 
		->setItemList($itemList) 
		->setDescription($description) 
		->setInvoiceNumber(uniqid());
	
}	private function getAmount($total=20,$details=""){
		$amount = new Amount(); 
		$amount->setCurrency("USD") 
		->setTotal($total) 
		->setDetails($details);
		return $amount;
	}
	private function getItems(){
		$item1 = new Item(); 
		$item1->setName('Ground Coffee 40 oz')
		->setCurrency('USD')
		->setQuantity(1)
		->setPrice(7.5); 

		$item2 = new Item(); 
		$item2->setName('Granola bars')
		->setCurrency('USD')
		->setQuantity(5)
		->setPrice(2); 
		$itemList = new ItemList(); 
		$itemList->setItems(array($item1, $item2));
	}
	private $apiContext=null;
	public static function getContext(){
		$apiContext = new ApiContext(
	        new OAuthTokenCredential(
	            Config::get('paypal.API.client_id'),
	            Config::get('paypal.API.secret')
	        )
	    );
	    
		$apiContext->setConfig(Config::get('paypal.config'));
		return $apiContext;
	}
	private function getApiContext(){
		if($this->apiContext!=null){
			return $this->apiContext;
		}
		$this->apiContext = new ApiContext(
	        new OAuthTokenCredential(
	            Config::get('paypal.API.client_id'),
	            Config::get('paypal.API.secret')
	        )
	    );
	    
		$this->apiContext->setConfig(Config::get('paypal.config'));
		return $this->apiContext;
	}
}
