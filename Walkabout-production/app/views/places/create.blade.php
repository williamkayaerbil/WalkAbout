@section('script')
<script src="{{asset('js/moment.js')}}"></script>
 <script src="{{asset('js/bootstrap-datetimepicker.min.js')}}"></script>
 <script src="{{asset('js/lib/ckeditor/ckeditor.js')}}"></script>
<script src="{{asset('js/script.js')}}"></script>
<link rel="stylesheet" type="text/css" href="{{asset('js/lib/fancybox/jquery.fancybox.css')}}" media="screen" />
<script type="text/javascript" src="{{asset('js/lib/fancybox/jquery.fancybox.pack.js')}}"></script>

<style type="text/css">
  .fancybox-inner{
    height: 100%;
  }
</style>
<script>
$(document).on('ready',function(){
    function getPlaces(layerId){
       var data={layer_id: layerId};
       @if($model->id)
       data.place_id={{$model->id}};
       @endif
        $.ajax({
           url: '{{url("admin/places/other-places")}}',
           data:data,
           type:'GET',
           dataType:'JSON'
        }).done(function(res){
            if(res&&res.length){
              Map.showPlaces(res);
            }
        });
    }
  $('.iframe-btn').fancybox({ 
    width   : 900,
    height  : 700,
    type    : 'iframe',
    fitToView   : false,
    autoScale     : false,
    autoSize : false
  });
  
  @if($model->layer_id)
     Map.showMarker();
     getPlaces({{$model->layer_id}});
  @endif

  CKEDITOR.replace( 'description',{
    language:'en'
  } );
   $("#layer_id").on('change',function(){
        getPlaces($(this).val());
    });
  $('#map_id').on('change',function(){
    Walkabout.fillSelect('layer_id',$(this).val(),"layers");
    Walkabout.getCategories($(this).val(),2);
    var jsMap={{$jsMap}};
    var id=$(this).val();
    var options=$.grep(jsMap,function(item){
        return parseInt(item.id,10)===parseInt(id,10)
    });

   
    if(options && options[0]){
      Map.setMapInfo(options[0]);  
      var center=options[0].center.split(',');
      $('input[name=lat]').val(center[0]);
      $('input[name=lng]').val(center[1]);
    }
    

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

  $("#permanent").on('change',function(){
     if(!$(this)[0].checked){
        $('.dates').show();
     }else{
         $('.dates').hide();
     }
  });

   $('#picture').on('change',function(){
      readURL(this,$("#preview"));
     // $(this).closest('.row').find('input[type=text]').val($(this).val());
   });

   $("form").on('submit',function(e){
      if(CKEDITOR.instances.description.invalid){
        e.preventDefault();
        alert("Characters limit reached in description text")
        return false;  
      }

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
      {{Form::select('map_id', $maps,$model->layer?$model->layer->map->id:null,array('class'=>'form-control','id'=>'map_id'))}}
    </div>
  </div>

   <div class="form-group col-md-12">
    <label for="layer_id" class="col-sm-2 control-label">Layer</label>
    <div class="col-sm-10">
      {{Form::select('layer_id',$model->layer?$model->layer->map->layers->lists('name','id'):$layers,null,array('class'=>'form-control','id'=>'layer_id'))}}
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

   <div class="col-sm-10 col-sm-offset-2 input-checkbox">
            <label for=""><input type="checkbox" value="1"  {{$model->permanent? 'checked="checked"':''}}  name="permanent" id="permanent"> Permanent</label>
            </div>
  <div class='col-sm-5 col-sm-offset-2 dates'  style="{{$model->permanent?'display:none':''}}">
           
            <label for="startDateTimePicker" class="col-sm-2 control-label">Start</label>
            <div class="form-group col-sm-10">
                <div class='input-group date' id='startDateTimePicker'>
                    {{Form::text('start',$model->start && $model->start!="0000-00-00 00:00:00"?$model->start:date('Y-m-d H:i:s'),array('class'=>'form-control datetime','data-date-format'=>'YYYY-MM-DD HH:mm:ss'))}}
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
        </div>

          <div class='col-sm-5 dates' style="{{$model->permanent?'display:none':''}}">
            <label for="startDateTimePicker" class="col-sm-2 control-label">End</label>
            <div class="form-group col-sm-10">
                <div class='input-group date' id='endDateTimePicker'>
                    {{Form::text('end',$model->end && $model->end!="0000-00-00 00:00:00"?$model->end:date('Y-m-d H:i:s'),array('class'=>'form-control datetime','data-date-format'=>'YYYY-MM-DD HH:mm:ss'))}}
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
    <label for="picture" class="col-sm-2 control-label">Picture</label>
    <div class="col-sm-10">
      {{Form::file('picture',array('id'=>'picture'))}}
    </div>
  </div>

      
  <div class="form-group col-md-12">  
    <div class="col-sm-offset-2">
   <!-- <a href="{{url('media/dialog?type=1')}}" class="btn iframe-btn" type="button">Open Filemanager</a>-->
@if($model->picture)
  <img width="50" id="preview" height="50" src="{{asset('img/places/')}}/{{$model->picture}}?r={{str_random(8)}}">
@else
   <img height="50" id="preview" src="{{asset('img/map_logos/default_photo.png')}}">
   @endif
    </div>
    </div>


 
 
    <fieldset class="col-md-12 label-left">
    <legend>Position</legend>   
     <div class="form-group col-md-6">
    <label for="lat" class="col-sm-2 control-label">Latitude</label>
    <div class="col-sm-10">
      {{Form::text('lat',$model->lat?$model->lat:'33.7733502',array('class'=>'form-control'))}}
    </div>
  </div>
   <div class="form-group col-md-6">
    <label for="lng" class="col-sm-2 control-label">Longitude</label>
    <div class="col-sm-10">
      {{Form::text('lng',$model->lng?$model->lng:'-84.36458219999997',array('class'=>'form-control'))}}
    </div>
  </div>
  @include('partials/map')
    </fieldset>
 
<button class="btn btn-primary" type="submit">Save</button>
{{ Form::close() }}

@endsection