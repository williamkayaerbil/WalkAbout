@extends('admin.index')
@section('script')
<script>
	$(document).on('ready',function(){
  		$('#map_id').on('change',function(){
	    	Walkabout.fillSelect('layer_id',$(this).val(),"layers",{id:'',name:"All"});
  		});
  		$('#layer_id').on('change',function(){
	    	Walkabout.fillSelect('place_id',$(this).val(),"places",{id:'',name:"All"});
  		});

  		/*$('#import_map_id').on('change',function(){
	    	Walkabout.fillSelect('import_layer_id',$(this).val(),"layers",{id:'',name:"Layer"});
  		});*/
  		/*$('#import_layer_id').on('change',function(){
	    	Walkabout.fillSelect('import_place_id',$(this).val(),"places",{id:'',name:"Place"});
  		});*/
	});
</script>
@endsection
@section('content')
@include('partials/alert')
<div class="row">
	<div class="col-md-12 filters">
		<form class="" role="form" method="GET">
			<div class="form-group col-md-2">
				<label class="" for="type" >Title</label>
				<div class="">
					{{Form::text('filter[title]',Input::get('filter')['title'],array('class'=>'form-control'))}}
				</div>
			</div>
			
			<div class="form-group col-md-2">
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
				<label class="" for="filter[place_id]" >Place</label>
				<div class="">
					{{Form::select('filter[place_id]', array(""=>'All') + $places, Input::get('filter')['place_id'],array('class'=>'form-control','id'=>'place_id'))}}
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
@endsection


@section('modal-import')
 	<form enctype="multipart/form-data" action="{{url('admin/events/import')}}" method="POST">
	

		<!--<div class="form-group col-md-12">
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
		</div>-->
		<!--<div class="form-group col-md-12">
			<label class="" for="place_id" >Place</label>
			<div class="">
				{{Form::select('place_id', array(""=>'Place') + $places,null,array('class'=>'form-control','id'=>'import_place_id'))}}
			</div>
		</div>-->
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
