<?php
class ReportsController extends BaseController{

	/*
	select users.id as user_id,users.email, groups.id as id_group, groups.name as name, count(maps.id) as maps, users.quota, users.expiration_date as Expiry
	 from users
	left join assigned_roles on assigned_roles.user_id=users.id 
	left join groups on groups.user_id=users.id
	left join users as uchild on uchild.group_id=groups.id
	left join maps on maps.user_id=uchild.id
	where assigned_roles.role_id=4
	and maps.deleted_at is null
	and uchild.deleted_at is null
	and users.deleted_at is null
	and groups.deleted_at is null
	group by users.id,groups.id
	*/
	public function index(){
		$table=User::leftJoin('assigned_roles','user_id','=','users.id')
				->leftJoin('groups','groups.user_id','=','users.id')
				->leftJoin('users as uchild','uchild.group_id','=','groups.id')
				->leftJoin('maps','maps.user_id','=','uchild.id')
				->where('assigned_roles.role_id','=','4')
				->whereNull('maps.deleted_at')
				->whereNull('uchild.deleted_at')
				->whereNull('users.deleted_at')
				->whereNull('groups.deleted_at')
				->whereNotNull('groups.id')
				->groupBy('users.id')
				->groupBy('groups.id')
				->select(DB::raw('users.id as user_id,users.email,users.type as type, groups.id as id_group, groups.name as name, count(maps.id) as maps, users.quota, users.expiration_date as expiry'))
				->get();
		
		$result=[];
		//foreach ($table as $key => $row) {
		//	$result[$row->user_id]=$row;
		//}
		
		return View::make('reports.index')
			   ->with('result',$table);
	}

}