<?php
if(Input::has('orderBy')){
	$orderBy=Input::get('orderBy');


}
?>
<div class="table-responsive">
<form id="form-table" method="POST" action="{{url(Route::getCurrentRoute()->getPath(),'delete')}}">
<table class="table table-condensed condensed" style="margin-top: 20px;">
	<thead>
		<tr>
			<th style=" min-width:0px;"></th>
			@foreach($columns as $col)
			<th {{ isset($col['attributes'])? $col['attributes']: '' }}><span class="order-icon">{{$col["text"]}}   
			@if(isset($col['orderBy']))
			<?php 
				$params=$_GET;
				$mode="desc";	
				if(isset($_GET['orderBy'][$col['orderBy']])){
					$mode=$params['orderBy'][$col['orderBy']];
					unset($params['orderBy'][$col['orderBy']]);
				}
				
				if($mode=="desc"){ 
					$mode="asc";
				}else{
					$mode="desc";
				}
			?>	
			 <a href="{{url(Route::getCurrentRoute()->getPath().'?orderBy['.$col['orderBy'].']='.$mode.'&'.http_build_query($params))}}">
			 	<i {{isset($_GET['orderBy'][$col['orderBy']])&&$mode=="desc"?'style="color:#00f"':''}} class="fa fa-caret-up"></i>
			 	<i {{isset($_GET['orderBy'][$col['orderBy']])&&$mode=="asc"?'style="color:#00f"':''}}  class="fa fa-caret-down"></i>
			 </a>
			@endif </span> </th>
			@endforeach
			<th>{{isset(Input::get('filter')['deleted'])?'Restore':'Edit'}}</th>
			
		</tr>
	</thead>
	<tbody>
		@foreach($data as $row)
		<tr >
			<td class="text-center" style="width: 5px;"><input type="checkbox" value="{{$row->id}}" name="delete[]"></td>
			@foreach($columns as $col)
			@if(isset($col['type']))
				@if($col['type']=="image")
					<td style="text-align:center; width:50px; height:50px"><img src="{{asset('img/'.Request::segment(2).'/'.$row[$col['name']])}}" width="50" height="50"></td>
				@endif
			@else
				@if(isset($col["value"]))
					<td style="{{isset($col['style'])?$col['style']:''}}">{{ $col["value"]( $row->{$col["name"]} ) }}</td>
				@else
					<td style="{{isset($col['style'])?$col['style']:''}}">{{ strip_tags($row->{$col["name"]}) }}</td>
				@endif
				
			@endif
			@endforeach
			@if(isset(Input::get('filter')['deleted']))
				<td>
				@if($singular_name=="Map" && Auth::user()->getAvailableMaps()=="0")
					<a href="#" data-toggle="tooltip" data-placement="left" title="Exceeded quota" ><i class="fa fa-reply"></i></a></td>
				@else
					<a href="{{url('admin/'.Request::segment(2).'/'.$row->id.'/restore')}}"><i class="fa fa-reply"></i></a></td>
				@endif


			@else
				<td><a href="{{url('admin/'.Request::segment(2).'/'.$row->id.'/edit')}}"><i class="fa fa-pencil"></i></a></td>
			@endif
		</tr>
		@endforeach
	</tbody>
</table>
</form>
</div>
<div class="col-md-12" style="text-align:center">
<div class="pagination">
    <ul>
		{{$data->appends(Input::all())->links()}}
    </ul>
</div>
</div>