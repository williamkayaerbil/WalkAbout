@extends('admin.index')
@section('content')
@include('partials/alert')
{{ Form::model($model,array('route' =>$route,"method"=>$method,"files"=>true ,"class"=>"form-horizontal")) }}
  <div class="form-group col-md-12">
    <label for="name" class="col-sm-2 control-label">Name</label>
    <div class="col-sm-10">
      {{Form::text('name',null,array('class'=>'form-control',"maxlength"=>40))}}
    </div>
  </div>

   <div class="form-group col-md-12">
    <label for="title" class="col-sm-2 control-label">Description</label>
    <div class="col-sm-10">
      {{Form::textarea('description',null,array('class'=>'form-control'))}}
    </div>
  </div>


   <div class="form-group col-md-12">
    <label for="map_id" class="col-sm-2 control-label">Map</label>
    <div class="col-sm-10">
      {{Form::select('map_id', $maps,null,array('class'=>'form-control'))}}
    </div>
  </div>


     <div class="form-group col-md-12">
      <label for="title" class="col-sm-2 control-label">Kml/Kmz Layer</label>
      <div class="col-sm-10">
       {{Form::file('kml_layer')}}
      </div>
    </div>
  @if($model->kml_layer)
  <div class="col-md-12">
    <a target="_blank" href="{{asset('kml_layers/'.$model->kml_layer)}}">{{$model->kml_layer}}</a>
    </div>
    @endif
    
    
      
<div class="form-group col-md-12">
      <label for="title" class="col-sm-2 control-label">GeoJson Layer</label>
      <div class="col-sm-10">
       {{Form::file('geojson_layer')}}
      </div>
    </div>

      @if($model->geojson_layer)
      <div class="col-md-12">
    <a target="_blank" href="{{asset('geojson_layers/'.$model->geojson_layer)}}">{{$model->geojson_layer}}</a>
    </div>
    @endif
  
  {{Form::save()}}
{{ Form::close() }}

@endsection