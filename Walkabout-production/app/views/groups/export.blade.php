@extends('admin.index')
@section('content')
@include('partials/alert')
<form action="">
  @if(Auth::user()->can('manage_groups'))
  

  <div class="row">
  <div class="form-group col-md-6">
    <label for="name" class="col-sm-2 control-label">Groups</label>
    <div class="col-sm-10">
      {{Form::select('group_id',array(''=>'All') + $groups,null,array('class'=>'form-control'))}}
    </div>
  </div>
  <div class="form-group col-md-6">
    <div class="checkbox">
      <label>
        <input type="checkbox" value="1" name="include_groups">
        Include groups
      </label>
    </div>
  </div>
</div>
  <div class="row">
  <div class="form-group col-md-6">
    <label for="name" class="col-sm-2 control-label">Users</label>
    <div class="col-sm-10">
      {{Form::select('group_id',array(''=>'All') + $groups,null,array('class'=>'form-control'))}}
    </div>
  </div>
  <div class="form-group col-md-6">
    <div class="checkbox">
      <label>
        <input type="checkbox" value="1" name="include_users">
        Include users
      </label>
    </div>
  </div>
</div>
  <div class="row">
  <div class="form-group col-md-6">
    <label for="name" class="col-sm-2 control-label">Maps</label>
    <div class="col-sm-10">
      {{Form::select('group_id',array(''=>'All') + $groups,null,array('class'=>'form-control'))}}
    </div>
  </div>
  <div class="form-group col-md-6">
    <div class="checkbox">
      <label>
        <input type="checkbox" value="1" name="include_maps">
        Include maps
      </label>
    </div>
  </div>
</div>

  <div class="row">
  <div class="form-group col-md-6">
    <label for="name" class="col-sm-2 control-label">Layers</label>
    <div class="col-sm-10">
      {{Form::select('group_id',array(''=>'All') + $groups,null,array('class'=>'form-control'))}}
    </div>
  </div>
  <div class="form-group col-md-6">
    <div class="checkbox">
      <label>
        <input type="checkbox" value="1" name="include_layers">
        Include layers
      </label>
    </div>
  </div>
</div>

  <div class="row">
  <div class="form-group col-md-6">
    <label for="name" class="col-sm-2 control-label">Places Categories</label>
    <div class="col-sm-10">
      {{Form::select('group_id',array(''=>'All') + $groups,null,array('class'=>'form-control'))}}
    </div>
  </div>
  <div class="form-group col-md-6">
    <div class="checkbox">
      <label>
        <input type="checkbox" value="1" name="include_places_categories">
        Include places categories
      </label>
    </div>
  </div>
</div>
  <div class="row">
  <div class="form-group col-md-6">
    <label for="name" class="col-sm-2 control-label">Places</label>
    <div class="col-sm-10">
      {{Form::select('group_id',array(''=>'All') + $groups,null,array('class'=>'form-control'))}}
    </div>
  </div>
  <div class="form-group col-md-6">
    <div class="checkbox">
      <label>
        <input type="checkbox" value="1" name="include_places">
        Include places
      </label>
    </div>
  </div>
</div>

  <div class="row">
  <div class="form-group col-md-6">
    <label for="name" class="col-sm-2 control-label">Events Categories</label>
    <div class="col-sm-10">
      {{Form::select('group_id',array(''=>'All') + $groups,null,array('class'=>'form-control'))}}
    </div>
  </div>
  <div class="form-group col-md-6">
    <div class="checkbox">
      <label>
        <input type="checkbox" value="1" name="include_events_categories">
        Include events categories
      </label>
    </div>
  </div>
</div>

  <div class="row">
  <div class="form-group col-md-6">
    <label for="name" class="col-sm-2 control-label">Events</label>
    <div class="col-sm-10">
      {{Form::select('group_id',array(''=>'All') + $groups,null,array('class'=>'form-control'))}}
    </div>
  </div>
  <div class="form-group col-md-6">
    <div class="checkbox">
      <label>
        <input type="checkbox" value="1" name="include_events">
        Include events
      </label>
    </div>
  </div>
</div>


  @endif

<buttn class="btn btn-primary" type="submit">Export</buttn>
</form>
@endsection