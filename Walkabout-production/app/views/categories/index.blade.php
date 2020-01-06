@extends('admin.index')
@section('content')
@include('partials/alert')
<div class="row">
	<div class="col-md-12 filters">
		<form class="" role="form" method="GET">

			<div class="form-group col-md-1">
				<label class="" for="type" >Type</label>
				<div class="">
					{{Form::select('filter[type]', array(""=>'All','1' => 'Event', '2' => 'Place'), Input::get('filter')['type'],array('class'=>'form-control'))}}
				</div>
			</div>
				<div class="form-group col-md-1">
				<label class="" for="type" >Code</label>
				<div class="">
				{{Form::text('filter[code]',Input::get('filter')['code'],array('class'=>'form-control','maxlength'=>'3'))}}
				</div>
			</div>
			<div class="form-group col-md-2">
				<label class="" for="type" >Title</label>
				<div class="">
				{{Form::text('filter[title]',Input::get('filter')['title'],array('class'=>'form-control'))}}
				</div>
			</div>
		
				<div class="form-group col-md-2">
				<label class="" for="type" >Tag</label>
				<div class="">
				{{Form::text('filter[tag]',Input::get('filter')['tag'],array('class'=>'form-control'))}}
				</div>
			</div>
				<div class="form-group col-md-2">
				<label class="" for="type" >Description</label>
				<div class="">
				{{Form::text('filter[description]',Input::get('filter')['description'],array('class'=>'form-control'))}}
				</div>
			</div>
			@if(Auth::user()->hasRole("Admin"))
			<div class="form-group col-md-2">
				<label class="" for="type" >Group</label>
				<div class="">
					{{Form::select('filter[group_id]', array(""=>'All')+$groups, Input::get('filter')['group_id'],array('class'=>'form-control'))}}
				</div>
			</div>
			@endif
			@include('partials/filter_buttons')
		</form>
	</div>
	</div>
@include('partials/top_buttons')
@include('partials/table')
@endsection



@section('modal-import')
 	<form enctype="multipart/form-data" action="{{url('admin/categories/import')}}" method="POST">
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
