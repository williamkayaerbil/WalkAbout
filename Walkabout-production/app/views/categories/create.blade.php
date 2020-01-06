@extends('admin.index')
@section('script')
<script>
  $(document).on('ready',function(){
    $(".icon-cat").on('click',function(){
      $("#input-icon").val($(this).data("icon"));
      $("#image").attr("src",$(this).attr("src"));
    });
    $("input[name='color']").attr("type","color");
    $("input[type='file']").on('change',function(){
      if($("#image").length){
          var oFReader = new FileReader();
          oFReader.readAsDataURL($(this)[0].files[0]);
          oFReader.onload = function (oFREvent) {
             $("#image").attr("src",oFREvent.target.result);
          };
      }
      if($("#input-icon").length){
        $("#input-icon").val("");
      }
    });
  });
</script>
@endsection
@section('content')
@include('partials/alert')
{{ Form::model($model,array('route' =>$route,"method"=>$method,"files"=>true ,"class"=>"form-horizontal",'autocomplete'=>'off')) }}

@if(count($groups)>0)
    
  <div class="form-group col-md-12">
    <label for="group_id" class="col-sm-2 control-label">Group</label>
    <div class="col-sm-10">
        {{Form::select('group_id', $groups,null,array('class'=>'form-control'))}}
    </div>
  </div>
@endif


  <div class="form-group col-md-12">
    <label for="title" class="col-sm-2 control-label">Title</label>
    <div class="col-sm-10">
      {{Form::text('title',null,array('class'=>'form-control',"maxlength"=>40))}}
    </div>
  </div>


 <div class="form-group col-md-12">
    <label for="title" class="col-sm-2 control-label">Code</label>
    <div class="col-sm-10">
       {{Form::text('code',null,array('class'=>'form-control',"maxlength"=>3))}}
    </div>
  </div>

   <div class="form-group col-md-12">
    <label for="title" class="col-sm-2 control-label">Color (HEX)</label>
    <div class="col-sm-10">
       {{Form::text('color',$model && $model->color? $model->color: "#333333",array('class'=>'form-control',"maxlength"=>3))}}
    </div>
  </div>

   <div class="form-group col-md-12">
    <label for="title" class="col-sm-2 control-label">Tag</label>
    <div class="col-sm-10">
      {{Form::text('tag',null,array('class'=>'form-control',"maxlength"=>30))}}
    </div>
  </div>

   <div class="form-group col-md-12">
    <label for="title" class="col-sm-2 control-label">Description</label>
    <div class="col-sm-10">
      {{Form::textarea('description',null,array('class'=>'form-control'))}}
    </div>
  </div>


   <div class="form-group col-md-12">
    <label for="title" class="col-sm-2 control-label">Type</label>
    <div class="col-sm-10">
      {{Form::select('type', array(1 => 'Event', 2 => 'Place'),null,array('class'=>'form-control'))}}
    </div>
  </div>

     <div class="form-group col-md-12">
    <label for="title" class="col-sm-2 control-label">Icon</label>
    <div class="col-sm-6">
     {{Form::file('image')}}
    </div>    
  </div>
<div class="form-group col-md-12">
    <div class="col-sm-offset-2">
      @if($model->image)
  
  <img width="50" id="image" height="50" src="{{asset('img/categories/')}}/{{$model->image}}?r={{str_random(8)}}">
 

    @else 
  <img width="50" id="image"  height="50" src="{{asset('img/categories/no-image.png')}}">

  @endif
     </div>
    </div>
<div class="col-sm-offset-2 col-sm-10">
<p>Or select an icon</p>
<ul id="icons" >
@foreach (Icon::all() as $key => $icon) 
  <li><img class="icon-cat" data-icon="{{$icon->name}}" src="{{asset('img/icons/'.$icon->name)}}"></li>
@endforeach
</ul>
<input id="input-icon" type="hidden" name="icon">
</div>
  {{Form::check("Trusted","trusted")}}
  {{Form::save()}}
{{ Form::close() }}

@endsection