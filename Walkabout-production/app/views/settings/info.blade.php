@extends('admin.index')
@section('content')
@include('partials/alert')

<div class="panel panel-default">
  <div class="panel-body">
  	<div class="row">
   		<div class="col-md-2"  style="text-align:right"><strong>Map into plan</strong></div>
   		<div class="col-md-2">	 {{($quota=Auth::user()->getQuota())==-1?"Unlimited":$quota}}</div>
   	</div>

   	 <div class="row">
   		<div class="col-md-2" style="text-align:right"><strong>Current maps</strong></div>
   		<div class="col-md-2">	 {{Auth::user()->getCurrentMaps()}}</div>
   	</div>

   	 <div class="row">
   		<div class="col-md-2" style="text-align:right"><strong>Available maps</strong></div>
   		<div class="col-md-2">	 {{Auth::user()->getAvailableMaps()}}</div>
   	</div>
   	<div class="row">
   		<div class="col-md-2" style="text-align:right"><strong>Expiration date</strong></div>
   		<div class="col-md-2">	 {{Auth::user()->expiration_date}}</div>
   	</div>
  </div>
</div>

@endsection