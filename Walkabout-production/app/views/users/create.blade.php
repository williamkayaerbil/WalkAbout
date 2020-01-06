@extends('admin.index')
@section('script')
<script src="{{asset('js/moment.js')}}"></script>
 <script src="{{asset('js/bootstrap-datetimepicker.min.js')}}"></script>
<script charset="utf-8">
  $(document).on('ready',function(){
      $("#role_id").on('change',function(){
          if(parseInt($(this).val(),10)===4){
            $("#groups").show('slow');
          }else{
            $("#groups").hide('slow');
          }

      });

      $('#expiration_date').datetimepicker({
              language: 'en',
              pick12HourFormat: false,
              pickDate: true,
              pickTime: false,
              useSeconds: true,
              use24hours:true

      });
  });
</script>
@endsection
@section('content')
@include('partials/alert')
{{ Form::model($model,array('route' =>$route,"method"=>$method,"files"=>true ,"class"=>"form-horizontal",'autocomplete'=>'off')) }}
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

  <div class="form-group col-md-12">
    <label for="name" class="col-sm-2 control-label">Password</label>
    <div class="col-sm-10">
      {{Form::password('password',array('class'=>'form-control'))}}
    </div>
  </div>

  <div class="form-group col-md-12">
    <label for="name" class="col-sm-2 control-label">Group</label>
    <div class="col-sm-10">
      {{Form::select('group_id', $groups,null,array('class'=>'form-control'))}}
    </div>
  </div>

    <div class="form-group col-md-12">
    <label for="name" class="col-sm-2 control-label">Role</label>
    <div class="col-sm-10">
      {{Form::select('role_id', $roles,$model&&isset($model->roles[0])?$model->roles[0]->id:null,array('class'=>'form-control','id'=>'role_id'))}}
    </div>
  </div>

<?php  //var_dump(Session::get('_old_input')); exit;?>
<div {{$model && isset($model->roles)  && isset($model->roles[0]) && $model->roles[0]->id==4 || Session::has('_old_input') && Session::get('_old_input')['role_id']=="4"?'':'style="display:none"' }} id="groups">

  <div class="form-group col-md-12"   >
    <label for="name" class="col-sm-2 control-label">Groups</label>
    <div class="col-sm-10">
      {{Form::select('groups[]', $groups_account,$model->groups->lists('id'),array('class'=>'form-control','multiple'=>true))}}
    </div>
  </div>
   <div class="form-group col-md-12">
    <label for="name" class="col-sm-2 control-label">Current maps</label>
    <div class="col-sm-10">
       <p class="form-control-static">{{!$model->name?0:  $model->getCurrentMaps($model)}}</p>
    </div>
  </div>
  <div class="form-group col-md-12">
    <label for="name" class="col-sm-2 control-label">Quota (Maps)</label>
    <div class="col-sm-10">
      {{Form::text('quota',null,array('class'=>'form-control'))}}
    </div>
  </div>

   <div class="form-group col-md-12">
    <label for="name" class="col-sm-2 control-label">Expiration date</label>
    <div class="col-sm-10">
      <div class='input-group date' id='expiration_date'>
          {{Form::text('expiration_date',$model->expiration_date&&$model->expiration_date!="0000-00-00"?$model->expiration_date:date('Y-m-d'),array('class'=>'form-control datetime','data-date-format'=>'YYYY-MM-DD'))}}
          <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
          </span>
      </div>
    </div>
  </div>



</div>

  {{Form::save()}}
{{ Form::close() }}

@endsection
