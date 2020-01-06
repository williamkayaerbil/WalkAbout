@extends('admin.index')
@section('content')
@include('partials/alert')
<div class="row">
	<div class="col-md-12 filters">
		<form class="" role="form" method="GET">
			<div class="form-group col-md-2">
				<label class="" for="type" >Name</label>
				<div class="">
					{{Form::text('filter[name]',Input::get('filter')['name'],array('class'=>'form-control'))}}
				</div>
			</div>
			
			<div class="form-group col-md-2">
				<label class="" for="type" >Email</label>
				<div class="">
				{{Form::text('filter[email]',Input::get('filter')['email'],array('class'=>'form-control'))}}
				</div>
			</div>

			<div class="form-group col-md-2">
				<label class="" for="type" >Group</label>
				<div class="">
				{{Form::select('filter[group_id]', array(""=>'All') + $groups, Input::get('filter')['group_id'],array('class'=>'form-control','id'=>'group_id'))}}
				</div>
			</div>

			<div class="form-group col-md-2">
				<label class="" for="type" >Role</label>
				<div class="">
				{{Form::select('filter[role_id]', array(""=>'All') + $roles, Input::get('filter')['role_id'],array('class'=>'form-control','id'=>'role_id'))}}
				</div>
			</div>
			@include('partials/filter_buttons')
		</form>
	</div>
	</div>
@include('partials/top_buttons')
@include('partials/table')
@endsection


@section('modal-import')
 	<form enctype="multipart/form-data" action="{{url('admin/users/import')}}" method="POST">
	@if(Auth::user()->hasRole("Admin"))
		<div class="form-group col-md-12">
			<label class="" for="group_id" >Group</label>
			<div class="">
				{{Form::select('group_id', array(""=>'Group') + $groups,null,array('class'=>'form-control','id'=>'import_group_id'))}}
			</div>
		</div>
		@endif
		
		 <div class="form-group col-md-2">
	      <label class="" for="file" >Delimiter</label>
	      <div class="">
	         <input type="text" class="form-control" style="text-align:center; font-size:22px" maxlength="1" name="delimiter" value=",">
	      </div>
    	</div>
    	 <div class="form-group col-md-12">
    	 <div class="checkbox">
			<label>
				<input type="checkbox" value="1" name="overwrite">
				Overwrite all
			</label>
		</div>
    	</div>
      <div class="form-group col-md-12">
	      <label class="" for="file" >CSV File</label>
	      <div class="">
	         <input type="file" name="file">
	      </div>
     </div>
      <div class="col-md-12" style="text-align:right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Import</button>
      </div>

	</form>
@endsection
