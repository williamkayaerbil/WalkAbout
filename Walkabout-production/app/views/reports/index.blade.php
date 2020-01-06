@extends('admin.index')
@section('content')


<table class="table table-condensed condensed" style="margin-top: 20px;">

<tr>
	<th>Email</th>
	<th>Account Type</th>
	<th>Groups</th>
	<th>Maps</th>
	<th>Quota</th>
	<th>Expiry</th>

</tr>
<?php $ant=""?>
@foreach($result as $k=>$user)

<tr>
  @if($user->email!=$ant)
   	<td>{{$user->email}}</td>
   	<td>{{$user->type=="1"?"Paypal":"Manual Account"}}</td>
    <td>{{$user->name}}</td>
    <td>{{$user->maps}}</td>
    <td>{{$user->quota}}</td>
    <td>{{$user->expiry}}</td>
   	@else
   	<td></td>
   	<td></td>
    <td>{{$user->name}}</td>
    <td>{{$user->maps}}</td>
    <td></td>
    <td></td>
   @endif


    

  
  <?php $ant=$user->email?> 
</tr>

@endforeach

</table>

@endsection