@extends('admin.index')
@section('script')

<script src="{{asset('js/moment.js')}}"></script>
<script src="{{asset('js/bootstrap-datetimepicker.min.js')}}"></script>
<script src="{{asset('js/script.js')}}"></script>

<script>  
$(document).on('ready',function(){
    Map.showMarker();
    $("#group_id").on('change',function(){
        Walkabout.fillSelect('user_id',$(this).val(),"users");
    });

   $('#startTimePicker').datetimepicker({
    language: 'en',
    pick12HourFormat: false,
    pickDate: false,
    pickTime: true,
    useSeconds: true,
    use24hours:true

  });
  $('#endTimePicker').datetimepicker({
    language: 'en',
    pick12HourFormat: false,
    pickDate: false,
    pickTime: true,
    useSeconds: true,
    use24hours:true
  });
  $("#embebed-code").html('<iframe src="{{url("map/".$model->hash)}}" style="height: 300px; width:200px;"></iframe>').on('click',function(){
    $(this).select()
  });
  $(".btn-size").on('click',function(){
    var size=$(this).text();
    var s=size.split('x');
    $("#embebed-code").html('<iframe src="{{url("map/".$model->hash)}}" style="height: '+s[0]+'px; width:'+s[1]+'px;"></iframe>');
    $("#size-w").val(s[0]);
    $("#size-h").val(s[1]);
  });
  $("#size-h,#size-w").on('change',function(){
    var h=$('#size-h').val();
    var w=$('#size-w').val();
   
    if(parseInt(h,10)&&parseInt(w,10)){
       $("#embebed-code").html('<iframe src="{{url("map/".$model->hash)}}" style="height: '+w+'px; width:'+h+'px;"></iframe>');
    }

  });

   $('.browse-file').on('click',function(){
     $(this).siblings('input[type=file]').trigger('click');
   });
   $('.file-image').on('change',function(){
      readURL(this,$(this).closest('.row').find('img'));
     // $(this).closest('.row').find('input[type=text]').val($(this).val());
   });

  $('#for-all').on('change',function(){
    if($(this)[0].checked){
       $('#logos').hide();
      
    }else{
      $('#logos').show();
    }
  });
  $('#add-more').on('click',function(){
    $('#menu-options').append('\
           <div class="row">\
        <div class="form-group col-md-4">\
            <label for="title" class="col-sm-2 control-label">Name</label>\
            <div class="col-sm-10">\
              <input type="text" name="menu_name[]" class="form-control">\
            </div>\
        </div>\
          <div class="form-group col-md-8">\
            <label for="title" class="col-sm-2 control-label">Website</label>\
            <div class="col-sm-10">\
             <input type="text" name="website_name[]" class="form-control">\
            </div>\
        </div>\
        </div>');
  })

});
</script>
<style type="text/css">
   .label-image{
    text-align: left !important;
   }
</style>
@endsection
@section('content')
@include('partials/alert')
{{ Form::model($model,array('route' =>$route,"method"=>$method,"files"=>true ,"class"=>"form-horizontal", "autocomplete"=>"off")) }}


@if(Auth::user()->hasRole("Admin") || Auth::user()->hasRole("Account Admin"))
  <div class="form-group col-md-12">
    <label for="group_id" class="col-sm-2 control-label">Group</label>
    <div class="col-sm-10">
        {{Form::select('group_id',array('Select Group') + $groups,$model->user?$model->user->group->id:Auth::user()->group_id,array('class'=>'form-control','id'=>'group_id'))}}
    </div>
  </div>
@endif


@if(Auth::user()->can("manage_maps"))
  <div class="form-group col-md-12">
    <label for="user_id" class="col-sm-2 control-label">Owner</label>
    <div class="col-sm-10">
        {{Form::select('user_id', $users,$model->user_id?$model->user_id:Auth::user()->id,array('class'=>'form-control','id'=>'user_id'))}}
    </div>
  </div>
@endif

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

<div class="row logo-wrapper" id="header-logo">
     <div class="col-md-4">
    <label for="title" class="col-sm-6 control-label label-image">Header logo</label>
    <button type="button" class="btn btn-primary browse-file">Browse</button>
     {{Form::file('header_logo',array('class'=>'hidden file-image'))}}

  </div>
  
  <div class="form-group col-md-8">
    <label for="title" class="col-sm-2 control-label">Url</label>
    <div class="col-sm-10">
      {{Form::text('header_logo_url',null,array('class'=>'form-control website'))}}
      <label><input type="checkbox" id="for-all" name="for_all" value="1"> Use for all</label>
    </div>

  </div>
    <div class="col-md-2 text-center" style="margin-bottom: 24px"><img  height="100" src="{{asset('img/map_logos/')}}/{{$model->header_logo?$model->header_logo:'default_photo.png'}}?r={{str_random(8)}}"></div>
    </div>
      
 <div id="logos">
 <div class="row logo-wrapper">
     <div class="col-md-4">
    <label for="title" class="col-sm-6 control-label label-image">Menu logo</label>
    <button type="button" class="btn btn-primary browse-file">Browse</button>
     {{Form::file('menu_logo',array('class'=>'hidden file-image'))}}
  </div>
  
  <div class="form-group col-md-8">
    <label for="title" class="col-sm-2 control-label">Url</label>
    <div class="col-sm-10">
      {{Form::text('menu_logo_url',null,array('class'=>'form-control website'))}}
    </div>
  </div>
    <div class="col-md-2 text-center" style="margin-bottom: 24px"><img  height="100" src="{{asset('img/map_logos/')}}/{{$model->menu_logo?$model->menu_logo:'default_photo.png'}}?r={{str_random(8)}}"></div>
    </div>
      
 
 <div class="row logo-wrapper">
     <div class="col-md-4">
    <label for="title" class="col-sm-6 control-label label-image">Place logo</label>
    <button type="button" class="btn btn-primary browse-file">Browse</button>
     {{Form::file('place_logo',array('class'=>'hidden file-image'))}}
  </div>
  
  <div class="form-group col-md-8">
    <label for="title" class="col-sm-2 control-label">Url</label>
    <div class="col-sm-10">
      {{Form::text('place_logo_url',null,array('class'=>'form-control website'))}}
    </div>
  </div>
    <div class="col-md-2 text-center" style="margin-bottom: 24px"><img  height="100" src="{{asset('img/map_logos/')}}/{{$model->place_logo?$model->place_logo:'default_photo.png'}}?r={{str_random(8)}}"></div>
    </div>
      
 
 <div class="row logo-wrapper">
     <div class="col-md-4">
    <label for="title" class="col-sm-6 control-label label-image">Event logo</label>
    <button type="button" class="btn btn-primary browse-file">Browse</button>
     {{Form::file('event_logo',array('class'=>'hidden file-image'))}}
  </div>
  
  <div class="form-group col-md-8">
    <label for="title" class="col-sm-2 control-label">Url</label>
    <div class="col-sm-10">
      {{Form::text('event_logo_url',null,array('class'=>'form-control website'))}}
    </div>
  </div>
    <div class="col-md-2 text-center" style="margin-bottom: 24px"><img  height="100" src="{{asset('img/map_logos/')}}/{{$model->event_logo?$model->event_logo:'default_photo.png'}}?r={{str_random(8)}}"></div>
    </div>
     </div> 
 

      <legend>
      Menu options
    </legend>
   <div id="menu-options">
    @if(!$model->menu_options)
    @for($i=0;$i<3;$i++)
    <div class="row">
        <div class="form-group col-md-4">
            <label for="title" class="col-sm-2 control-label">Name</label>
            <div class="col-sm-10">
              <input type="text" name="menu_name[]" class="form-control">
            </div>
        </div>
          <div class="form-group col-md-8">
            <label for="title" class="col-sm-2 control-label">Website</label>
            <div class="col-sm-10">
            <input type="text" name="website_name[]" class="form-control">
            </div>
        </div>
    </div>
    @endfor
    @else
    @foreach(json_decode($model->menu_options) as $k=>$option )
       <div class="row">
        <div class="form-group col-md-4">
            <label for="title" class="col-sm-2 control-label">Name</label>
            <div class="col-sm-10">
            <input type="text" name="menu_name[]" class="form-control" value="{{$option->name}}">
            </div>
        </div>
          <div class="form-group col-md-8">
            <label for="title" class="col-sm-2 control-label">Website</label>
            <div class="col-sm-10">
            <input type="text" name="website_name[]" class="form-control" value="{{$option->url}}">
            </div>
        </div>
    </div>
    @endforeach
    @endif
    </div>
      <button type="button" class="btn btn-primary" id="add-more">Add more</button>


    <fieldset class="col-md-12 label-left">
    	<legend>Map options</legend>
      <div class="row">
  			<div class="form-group col-md-6">
      			<label for="zoom_ctrl_pos" class="col-sm-2 control-label">Zoom Control</label>
      			<div class="col-sm-10">
       				{{Form::select('zoom_ctrl_pos',$positions,null,array("class"=>'form-control','id'=>'zoom_ctrl_pos'))}}
      			</div>
    		</div>

			<div class="form-group col-md-6">
      			<label for="maptype_ctrl_pos" class="col-sm-2 control-label">Map Type</label>
      			<div class="col-sm-10">
       				{{Form::select('maptype_ctrl_pos',$positions,null,array("class"=>'form-control','id'=>'maptype_ctrl_pos'))}}
      			</div>
    		</div>
        </div>

   <div class="row">
		<div class="form-group col-md-6">
      			<label for="pan_ctrl_pos" class="col-sm-2 control-label">Pan Control</label>
      			<div class="col-sm-10">
       				{{Form::select('pan_ctrl_pos',$positions,null,array("class"=>'form-control','id'=>'pan_ctrl_pos'))}}
      			</div>
    		</div>

    		<div class="form-group col-md-6">
      			<label for="streetview_ctrl_pos" class="col-sm-2 control-label">StreetView Control</label>
      			<div class="col-sm-10">
       				{{Form::select('streetview_ctrl_pos',$positions,null,array("class"=>'form-control','id'=>'streetview_ctrl_pos'))}}
      			</div>
    		</div>
        </div>
         <div class="row">
    	   <div class="form-group col-md-6">
      			<label for="title" class="col-sm-2 control-label">Zoom</label>
      			<div class="col-sm-10">
       				{{Form::select('zoom',range(0, 21),$model->zoom?$model->zoom:"18",array("class"=>'form-control'))}}
      			</div>
    		</div>
    		 <div class="form-group col-md-6">
      			<label for="title" class="col-sm-2 control-label">Center</label>
      			<div class="col-sm-10">
       				{{Form::text('center',$model->center?$model->center:'33.7733502,-84.36458219999997',array('class'=>'form-control','id'=>'center'))}}
      			</div>
    		</div>
        </div>
        
        <div class="row">
         <div class="form-group col-md-6">
            <label for="title" class="col-sm-2 control-label">Client Zoom</label>
            <div class="col-sm-10">
              {{Form::text('client_zoom',$model->client_zoom?$model->client_zoom:'19',array('class'=>'form-control','id'=>'client_zoom'))}}
            </div>
        </div>

        </div>
 <div class="row">
         <div class="form-group col-md-3">
            <label for="title" class="col-sm-5 control-label">Click tracking</label>
            <div class="col-sm-3">
               {{Form::checkbox('click_event', 1,$model->id?$model->click_event:1);}}
            </div>
        </div>
           <div class="form-group col-md-3">
            <label for="title" class="col-sm-5 control-label">Navigation tracking</label>
            <div class="col-sm-3">
            {{Form::checkbox('navigation_event', 1,$model->id?$model->navigation_event:1);}}
            </div>
        </div>
          <div class="form-group col-md-6">
            <label for="title" class="col-sm-3 control-label">Seconds to send interaction tracking</label>
            <div class="col-sm-9">
              {{Form::number('navigation_time',$model->navigation_time?$model->navigation_time:'30',array('class'=>'form-control','id'=>'navigation_time'))}}
            </div>
        </div>
        </div>
  <legend>Time Slider</legend>
    <div class="row">
         <div class="form-group col-md-6">
            <label for="title" class="col-sm-2 control-label">Start time</label>
            <div class="col-sm-10">
                 <div class='input-group date' id='startTimePicker'>
                    {{Form::text('start_time',$model->start_time?$model->start_time:'08:00:00',array('class'=>'form-control datetime','data-date-format'=>'HH:mm:ss'))}}
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label for="title" class="col-sm-2 control-label">Final time</label>
            <div class="col-sm-10">
              <div class='input-group date' id='endTimePicker'>
                    {{Form::text('final_time',$model->final_time?$model->final_time:'20:00:00',array('class'=>'form-control datetime','data-date-format'=>'HH:mm:ss'))}}
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
        </div>
        </div>
        <div class="row">
          <div class="form-group col-md-6">
            <label for="title" class="col-sm-3 control-label">Step in minutes</label>
            <div class="col-sm-9">
              {{Form::number('step',$model->step?$model->step:'60',array('class'=>'form-control','id'=>'step'))}}
            </div>
        </div>
          
        </div>
    @if($model->hash)
         <div class="row">
           <label for="title" class="col-sm-2 control-label">Size</label>
          <div class="col-md-9">
             <div class="col-md-6">
             <div class="btn-group" role="group" aria-label="...">
              <button type="button" class="btn btn-default btn-size">300x200</button>
              <button type="button" class="btn btn-default btn-size">480x300</button>
              <button type="button" class="btn btn-default btn-size">640x400</button>
            </div>
            </div>
            <div class="col-md-2">
            <label>Custom</label>
            </div>
            <div class="col-md-2">
              <input class="form-control" id="size-w" value="300" placeholder="Width">  
            </div>
             <div class="col-md-2">
             <input class="form-control" id="size-h" value="200" placeholder="Height">
            </div>
            
          </div>
          <hr>
           <div class="form-group col-md-12">
           <label for="title" class="col-sm-2 control-label">Embed Code</label>
       
              <div class="col-sm-9">
              <textarea  style="cursor:pointer; background:#fff" readonly class="form-control disabled" id="embebed-code">
             
              </textarea>
              </div>

      
          
           </div>
         </div>
     @endif
       

        @include('partials/map')
    </fieldset>
  <div class="form-group">
    <button type="submit" class="btn btn-primary">Save</button>
  </div>
{{ Form::close() }}

@endsection