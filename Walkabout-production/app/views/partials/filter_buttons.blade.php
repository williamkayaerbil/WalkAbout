

<div class="filter-buttons col-md-12 " >
	{{Form::checkbox('filter[deleted]',1,isset(Input::get('filter')['deleted']),array("style"=>'display:none','id'=>'checkbox-deleted'))}}
	
	<div class="col-md-12">
		<div class="btn-group col-md-2" role="group" aria-label="...">
			<button class="btn btn-primary btn-md  " type="submit"><i class="fa fa-search"></i> Search </button>
			<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
			<span class="caret"></span>
			<span class="sr-only">Toggle Dropdown</span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li><a  class="{{isset(Input::get('filter')['deleted'])?'active':''}} " id="btn-deleted"><i class="fa {{isset(Input::get('filter')['deleted'])?'fa-check-square-o':'fa-square-o'}}"></i> Show deleted items</a></li>
				<li><a href="{{Request::url()}}" class="" ><i class="fa fa-eraser"> Clean search</i></a></li>
			</ul>
		</div>
		
		@if($singular_name=="Map" && Auth::user()->getAvailableMaps()=="0")
			<span  data-toggle="tooltip" data-placement="left" title="Exceeded quota" ><a class="btn btn-primary btn-md disabled " href="#"><i class="fa fa-plus"></i> Add {{$singular_name}}</a></span>
		@else
			<a class="btn btn-primary btn-md "   href="{{url(Route::getCurrentRoute()->getPath())}}/create"><i class="fa fa-plus"></i> Add {{$singular_name}}</a>
		@endif
		


<div style="position:relative; display:inline">
  <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
    <i class="fa fa-file-excel-o"></i> Import/Export
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
  	@if(Request::segment(2)=="places")
    <li role="presentation"><a role="menuitem" tabindex="-1"  data-toggle="modal" data-target="#modal-import-photos" href="#"><i class="fa fa-camera"></i> Import from images</a></li>
    @endif
    <li role="presentation"><a role="menuitem" tabindex="-1"  data-toggle="modal" data-target="#modal-import" href="#"><i class="fa fa-file-excel-o"></i> Import</a></li>
    <li role="presentation"><a role="menuitem" tabindex="-1" href="{{url(Route::getCurrentRoute()->getPath())}}/export?{{http_build_query(Input::all())}}"><i class="fa fa-file-excel-o"></i> Export</a></li>
    <li role="presentation"><a role="menuitem" tabindex="-1" href="{{asset('templates')}}/{{Request::segment(2)}}_template.csv"><i class="fa fa-download"></i> Blank Template</a></li>
    <li role="presentation"><a role="menuitem" tabindex="-1" href="{{asset('templates')}}/{{Request::segment(2)}}_starterkit.pdf"> <i class="fa fa-download"></i> Starter kit</a></li>
  </ul>
</div>


		<!--<a href="{{Request::url()}}/export" class="btn btn-primary btn-md" title="Import from CSV"><i class="fa fa-file-excel-o"></i> Import/Export CSV</a>-->
		<a id="remove-item" title="Remove item" class="btn btn-primary btn-md" href="#"><i class="fa fa-trash"></i> Send to trash </a>
		
	</div>
</div>
