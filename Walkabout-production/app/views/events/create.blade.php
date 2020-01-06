@section('script')
<script src="{{asset('js/moment.js')}}"></script>
 <script src="{{asset('js/bootstrap-datetimepicker.min.js')}}"></script>
 <script src="{{asset('js/lib/ckeditor/ckeditor.js')}}"></script>
<script>
$(document).on('ready',function(){
  CKEDITOR.replace( 'description',{
    language:'en'
  } );
  $('#map_id').on('change',function(){
    Walkabout.fillSelect('layer_id',$(this).val(),"layers");
    Walkabout.getCategories($(this).val(),1);

  });
  $('#layer_id').on('change',function(){
    Walkabout.fillSelect('place_id',$(this).val(),"places");
  });
   $('#startDateTimePicker').datetimepicker({
    language: 'en',
    pick12HourFormat: false,
    pickDate: true,
    pickTime: true,
    useSeconds: true,
    use24hours:true

  });
  $('#endDateTimePicker').datetimepicker({
    language: 'en',
    pick12HourFormat: false,
    pickDate: true,
    pickTime: true,
    useSeconds: true,
    use24hours:true
  });

   $('#pic').on('change',function(){

      readURL(this,$("#preview"));
   });
});

 

</script>
@endsection

@extends('admin.index')
@section('content')
@include('partials/alert')
{{ Form::model($model,array('route' =>$route,"method"=>$method,"files"=>true ,"class"=>"form-horizontal",'autocomplete'=>'off')) }}


  <div class="form-group col-md-12">
    <label for="title" class="col-sm-2 control-label">Title</label>
    <div class="col-sm-10">
      {{Form::text('title',null,array('class'=>'form-control',"maxlength"=>40))}}
    </div>
  </div>
 <div class="form-group col-md-12">
    <label for="map_id" class="col-sm-2 control-label">Map</label>
    <div class="col-sm-10">
      {{Form::select('map_id', $maps,$model->place?$model->place->layer->map->id:null,array('class'=>'form-control','id'=>'map_id'))}}
    </div>
  </div>

   <div class="form-group col-md-12">
    <label for="layer_id" class="col-sm-2 control-label">Layer</label>
    <div class="col-sm-10">
      {{Form::select('layer_id',$model->place?$model->place->layer->map->layers->lists('name','id'):$layers,$model->place?$model->place->layer->id:null,array('class'=>'form-control','id'=>'layer_id'))}}
    </div>
  </div>
  
   <div class="form-group col-md-12">
    <label for="layer_id" class="col-sm-2 control-label">Place</label>
    <div class="col-sm-10">
      {{Form::select('place_id',$model->place?$model->place->layer->places->lists('clean_title','id'):$places,null,array('class'=>'form-control','id'=>'place_id'))}}
    </div>
  </div>
  <div class="form-group col-md-12">
    <label for="category_id" class="col-sm-2 control-label">Category</label>
    <div class="col-sm-10">
      {{Form::select('category_id', $categories,null,array('class'=>'form-control','id'=>'category_id'))}}
    </div>
  </div>
   <div class="form-group col-md-12">
    <label for="title" class="col-sm-2 control-label">Description</label>
    <div class="col-sm-10">
      {{Form::textarea('description',null,array('class'=>'form-control','id'=>'description','maxlength'=>250))}}
    </div>
  </div>

  <div class='col-sm-5 col-sm-offset-2'>
            <label for="startDateTimePicker" class="col-sm-2 control-label">Start</label>
            <div class="form-group col-sm-10">
                <div class='input-group date' id='startDateTimePicker'>
                    {{Form::text('start',$model->start?$model->start:date('Y-m-d H:i:s'),array('class'=>'form-control datetime','data-date-format'=>'YYYY-MM-DD HH:mm:ss'))}}
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
        </div>

          <div class='col-sm-5'>
            <label for="startDateTimePicker" class="col-sm-2 control-label">End</label>
            <div class="form-group col-sm-10">
                <div class='input-group date' id='endDateTimePicker'>
                    {{Form::text('end',$model->end?$model->end:date('Y-m-d H:i:s'),array('class'=>'form-control datetime','data-date-format'=>'YYYY-MM-DD HH:mm:ss'))}}
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
        </div>

  <div class="form-group col-md-12">
  <i class="fa fa-facebook icon"></i>
    <label for="category_id" class="col-sm-2 control-label"> Facebook</label>
    <div class="col-sm-10">
      {{Form::text('facebook',null,array('class'=>'form-control',"maxlength"=>255))}}
    </div>
  </div>

    <div class="form-group col-md-12">
     <i class="fa fa-twitter icon"></i>
    <label for="twitter" class="col-sm-2 control-label">Twitter</label>
    <div class="col-sm-10">
      {{Form::text('twitter',null,array('class'=>'form-control',"maxlength"=>255))}}
    </div>
  </div>

    <div class="form-group col-md-12">
     <i class="fa fa-instagram icon"></i>
    <label for="instagram" class="col-sm-2 control-label">Instagram</label>
    <div class="col-sm-10">
      {{Form::text('instagram',null,array('class'=>'form-control',"maxlength"=>255))}}
    </div>
  </div>

  <div class="form-group col-md-12">
    <label for="picture" id="picture" class="col-sm-2 control-label">Picture</label>
    <div class="col-sm-10">
      {{Form::file('picture',array('id'=>'pic'))}}
    </div>
  </div>

      
  <div class="form-group col-md-12">
    <div class="col-sm-offset-2">
    @if($model->picture)
      <img width="50" id="preview" height="50" src="{{asset('img/events/')}}/{{$model->picture}}?r={{str_random(8)}}">
    @else
    <img height="50" id="preview" src="{{asset('img/map_logos/default_photo.png')}}">
  @endif
    </div>
    </div>
  
 
 
<button class="btn btn-primary" type="submit">Save</button>
{{ Form::close() }}

@endsection