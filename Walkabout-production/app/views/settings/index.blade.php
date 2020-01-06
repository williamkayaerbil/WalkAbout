@extends('admin.index')
@section('content')
@include('partials/alert')

{{ Form::model(Auth::user(),array('route' =>'admin.settings.store',"method"=>'POST',"files"=>true ,"class"=>"form-horizontal")) }}
<legend>Profile</legend>
  <div class="form-group col-md-12">
    <label for="name" class="col-sm-2 control-label">Name</label>
    <div class="col-sm-10">
      {{Form::text('name',null,array('class'=>'form-control'))}}
    </div>
  </div>
	
    <div class="form-group col-md-12">
    <label for="name" class="col-sm-2 control-label">Email</label>
    <div class="col-sm-10">
      {{Form::text('email',null,array('class'=>'form-control'))}}
    </div>
  </div>

<legend>Change password</legend>
 


  <div class="form-group col-md-12">
    <label for="name" class="col-sm-2 control-label">New password</label>
    <div class="col-sm-10">
      {{Form::password('password',array('class'=>'form-control'))}}
    </div>
  </div>

  <div class="form-group col-md-12">
    <label for="name" class="col-sm-2 control-label">Confirm password</label>
    <div class="col-sm-10">
      {{Form::password('password_confirmation',array('class'=>'form-control'))}}
    </div>
  </div>




  {{Form::save()}}
{{ Form::close() }}

@endsection
