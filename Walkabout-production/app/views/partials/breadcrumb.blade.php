 @if(Request::segment(2))
	 <ol class="breadcrumb">
	        <li><a href="{{url('admin')}}">Home</a></li>	


			@if(Request::segment(3))
				<li><a href="{{url('admin',Request::segment(2))}}">{{ucfirst(Request::segment(2))}}</a></li>
				<li class="active">{{isset($name)?strip_tags($name):ucfirst(Request::segment(3))}}</li>
			@else
				@if(Request::segment(2))
					<li class="active">{{ucfirst(Request::segment(2))}}</li>
				@endif
			@endif

				
	 </ol>
 @endif