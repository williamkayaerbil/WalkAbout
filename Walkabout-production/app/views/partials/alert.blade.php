
@if(count($errors)>0)
<div class="alert alert-danger" role="alert">
	@foreach ($errors->all() as $message)
	  <p><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
	  {{$message}}</p>
	@endforeach
</div>
@endif

@if(Session::has('message'))
<div class="alert alert-success" role="alert">
	  <p>{{Session::pull('message')}}</p>
</div>
@endif

@if(Session::has('error'))
<div class="alert alert-danger" role="alert">
  <p><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
	  {{Session::pull('error')}}</p>
</div>
@endif