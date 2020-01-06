@extends('admin.index')
@section('script')
<link rel="stylesheet" href="{{asset('css/basic.css')}}">
<link rel="stylesheet" href="{{asset('css/dropzone.css')}}">
<script src="{{asset('js/lib/dropzone.js')}}"></script>
<script>
	Dropzone.options.photos=false;
	$(document).on('ready',function(){

  		$('#map_id').on('change',function(){
	    	Walkabout.fillSelect('layer_id',$(this).val(),"layers",{id:'',name:"All"});
  		});
  		$('#import_map_id').on('change',function(){
	    	Walkabout.fillSelect('import_layer_id',$(this).val(),"layers",{id:'',name:"Layer"});
  		});
  		$('#photos_map_id').on('change',function(){
	    	Walkabout.fillSelect('photos_layer_id',$(this).val(),"layers",{id:'',name:"Layer"});
  		});


  		var myDropzone = new Dropzone("#photos",{
  			addRemoveLinks: true,
		    acceptedFiles: 'image/jpg,image/jpeg'
  		});
		$("#save-photos").on('click',function(){
			var map= $('#photos_map_id').val(), layer=$('#photos_layer_id').val(),category=$('#photos_category_id').val();
			if(myDropzone.getQueuedFiles().length>0 || myDropzone.getUploadingFiles().length>0 || 
			   map==="" || layer===""  || category===""
				){
				return;
			}

			var accepted=myDropzone.getAcceptedFiles(),
				rejected=myDropzone.getRejectedFiles();
			if(accepted.length + rejected.length ===0){
				return;
			}
			$.ajax({
				url:'{{url("admin/places/save-uploaded-photos")}}',
				method:'POST',
				data:{
					accepted: accepted.map(function(img){ return img.name}),
					rejected: rejected.map(function(img){ return img.name}),
					map_id:map,
					layer_id:layer,
					category_id:category
				}
			}).done(function(){
				document.location.reload();
			});
			
		});
		
	});
</script>
@endsection
@section('content')
@include('partials/alert')
<div class="row">
	<div class="col-md-12 filters">
		<form class="" role="form" method="GET">
			<div class="form-group col-md-3">
				<label class="" for="type" >Title</label>
				<div class="">
					{{Form::text('filter[title]',Input::get('filter')['title'],array('class'=>'form-control'))}}
				</div>
			</div>
			
			<div class="form-group col-md-3">
				<label class="" for="filter[description]" >Description</label>
				<div class="">
					{{Form::text('filter[description]',Input::get('filter')['description'],array('class'=>'form-control'))}}
				</div>
			</div>

			<div class="form-group col-md-2">
				<label class="" for="filter[map]" >Map</label>
				<div class="">
				{{Form::select('filter[map_id]', array(""=>'All') + $maps, Input::get('filter')['map_id'],array('class'=>'form-control','id'=>'map_id'))}}
				</div>
			</div>

			<div class="form-group col-md-2">
				<label class="" for="filter[layer_id]" >Layer</label>
				<div class="">
					{{Form::select('filter[layer_id]', array(""=>'All') + $layers, Input::get('filter')['layer_id'],array('class'=>'form-control','id'=>'layer_id'))}}
				</div>
			</div>

			<div class="form-group col-md-2">
				<label class="" for="filter[category_id]" >Category</label>
				<div class="">
					{{Form::select('filter[category_id]', array(""=>'All') + $categories, Input::get('filter')['category_id'],array('class'=>'form-control'))}}
				</div>
			</div>
	
			@include('partials/filter_buttons')
		</form>
	</div>
</div>
@include('partials/top_buttons')
@include('partials/table')


<div class="modal fade" id="modal-import-photos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" >Import from geotagged photos</h4>

      </div>
      <div class="modal-body">
      <div class="col-md-12" style="height: 260px;">
      <div class="form-group col-md-12">
			<label class="" for="map_id" >Map</label>
			<div class="">
				{{Form::select('map_id', array(""=>'Map') + $maps,null,array('class'=>'form-control','id'=>'photos_map_id'))}}
			</div>
		</div>

		<div class="form-group col-md-12">
			<label class="" for="layer_id" >Layer</label>
			<div class="">
				{{Form::select('layer_id', array(""=>'Layer') + $layers,null,array('class'=>'form-control','id'=>'photos_layer_id'))}}
			</div>
		</div>
		<div class="form-group col-md-12">
				<label class="" for="category_id" >Category</label>
				<div class="">
					{{Form::select('category_id', array(""=>'Category') + $categories, null,array('class'=>'form-control','id'=>'photos_category_id'))}}
				</div>
			</div>
       <div style="display:block; float:left; margin-right: 20px;">
     <button class="btn btn-primary" id="save-photos">Save</button>
     </div>
     <div class="col-md-12">
        <p>Only JPG files</p> 
       </div>
     </div>
     <div class="col-md-12">
      <form action="{{url('admin/places/upload-photos')}}" method="POST" enctype="multipart/form-data" id="photos" class="dropzone">

      	  <div class="fallback">
    			<input name="file" type="file" multiple />
  		  </div>
      </form>
      </div>
         <div class="modal-footer">
         </div>
      </div>
    </div>
  </div>
</div>


@endsection



@section('modal-import')
 	<form enctype="multipart/form-data"  action="{{url('admin/places/import')}}" method="POST">
	

		<div class="form-group col-md-12">
			<label class="" for="map_id" >Map</label>
			<div class="">
				{{Form::select('map_id', array(""=>'Map') + $maps,null,array('class'=>'form-control','id'=>'import_map_id'))}}
			</div>
		</div>

		<div class="form-group col-md-12">
			<label class="" for="layer_id" >Layer</label>
			<div class="">
				{{Form::select('layer_id', array(""=>'Layer') + $layers,null,array('class'=>'form-control','id'=>'import_layer_id'))}}
			</div>
		</div>
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
