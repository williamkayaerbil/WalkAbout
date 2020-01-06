@extends('admin.index')
@section('content')
@include('partials/alert')
{{ Form::model($model,array('route' =>$route,"method"=>$method,"files"=>true ,"class"=>"form-horizontal",'autocomplete'=>'off')) }}
  <div class="form-group col-md-12">
    <label for="name" class="col-sm-2 control-label">Name</label>
    <div class="col-sm-10">
      {{Form::text('name',null,array('class'=>'form-control',"maxlength"=>30))}}
    </div>
  </div>



   <div class="form-group col-md-12">
    <label for="title" class="col-sm-2 control-label">Description</label>
    <div class="col-sm-10">
      {{Form::textarea('description',null,array('class'=>'form-control'))}}
    </div>
  </div>
  {{Form::save()}}
{{ Form::close() }}

@endsection