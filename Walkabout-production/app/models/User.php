<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Zizaco\Entrust\HasRole;
class User extends Eloquent implements UserInterface, RemindableInterface {
	use SoftDeletingTrait;
	use UserTrait, RemindableTrait;
 	use HasRole;

 		// Add your validation rules here
	public static $rules = array(
		'name' => 'required',
		'email'=>'required|email|unique:users,email,:id',
		'quota'=>'integer'
	);
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');
	public function maps()
	{
		return $this->hasMany('Map');
	}

	protected $fillable = ['name','email','password','quota','expiration_date','group_id','location','lat','lng','website','verified','category_id'];
	public function group()
	{
		return $this->belongsTo('Group')->withTrashed();
	}
	public function groups(){
		return $this->hasMany('Group','user_id','id');//->withTrashed();
	}
	public function roles()
    {
        return $this->belongsToMany('Role', 'assigned_roles', 'user_id', 'role_id');
    }
     public function setPasswordAttribute($value)
 	{
		$this->attributes['password'] = Hash::make($value);
 	}
 	private $groupIds=array();
 	public function getGroupIds(){
 		if(count($this->groupIds)==0){
 			$this->groupIds=$this->groups->lists('id');	
			if(count($this->groupIds)==0){
				$this->groupIds=array("-1");
			}
 		}
 		return $this->groupIds;
 	}
 	public function getCurrentMaps($user=null){

 		if($user==null){
 			$user=Auth::user();
 		}
 		
 		if($user->hasRole('Admin')){
			return Map::count();
		}else{
			if(!$user->hasRole('Account Admin')){
				$user=Auth::user()->group->user;	
			}
			if($user==null){
			    return count(Map::getBuilder()->get());
			}
			return count(Map::leftJoin('users','users.id','=','maps.user_id')
			->whereIn('users.group_id',$user->getGroupIds())->whereNull('maps.deleted_at')->get());

		}
 	}

 	public function isActive(){

 		 if($this->type==2){ //Paypal user
 		 	if(!$this->paypal_id) return true; //check temporal maps
            	$agreement=PaymentsController::getAgreement($this->paypal_id);

            	$state=$agreement->state;
            
                if($state==PaymentsController::$stateActive){
                	   return true;
                }

                if($state==PaymentsController::$statePending){
                	return false;
                }

                if($state==PaymentsController::$stateCancelled){

                	$lastPayment=$agreement->agreement_details->last_payment_date;
                	$paymentTime=strtotime($lastPayment);
                	$expirationTime=strtotime($lastPayment." + 1".strtoupper($agreement->plan->payment_definitions[0]->frequency));
                	$now=strtotime('now');
     

                	if($now>=$expirationTime || true ){
                		return false;
                	}
                	return true;
                }


            }
            return true;
 	}

 	public function getQuota($user=null){
 		if($user==null){
 			$user=Auth::user();
 		}
 		if($user->hasRole('Admin')){
 			return -1; //unlimited
 		}
 		if($user->hasRole('Account Admin')){
 			return $user->quota==0||$user->quota==null?-1:$user->quota;
 		}
 		if($user->group->user==null){
 			return 0;
 		}
		$q=$user->group->user->quota;
 		return $q==0||$q==null?-1:$q;
 		
 	}
 	public function getAvailableMaps(){
 		$quota=$this->getQuota();
 		if($quota==-1){
 			return "Unlimited";
 		}
		$max=$quota-$this->getCurrentMaps();
		if($max<0){
			return 0;
		}
		return $max;
	
 	}
}
