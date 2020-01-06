<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Walkabout - {{$map->name}}</title>
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet">
		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">

	<!--[if lt IE 9]>
  		<script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/r29/html5.min.js"></script>
  	<![endif]-->
  	<link rel="stylesheet" href="{{asset('css/bootstrap-slider.css')}}">
  	<meta name="viewport" content="width=device-width, user-scalable=no,initial-scale=1">
  	<style>

  body, html {
  margin: 0;
  padding: 0;
  width: 100%;
  font-size: 12px;
  color: #00457D;
  font-family: 'Verdana';
  height: 100%;
}
body { 
   overflow-x: hidden;
   overflow-y: hidden;
    
}
#menu-top{
	position: absolute;
	width: 100%;
	height: 40px;
	background: #428bca;
	z-index: 100;
	bottom:0;
	top:0;
}
.slider-handle{
width: 30px;
height: 30px;
margin-top: -10px !important;
opacity: 1;
background: #5bc0de;
}
#menu-bottom{
	position: absolute;
	width: 100%;
	height: 40px;
	background: #428bca;
	z-index: 100;
	bottom:0;
	left:0;
}
#timepicer-container{
  width: 60%;
  margin:8px auto 0 auto;
  min-width: 0;
}
#timeslider{

  width: 100%;
}
#menu-top h1{
	color:white;
	font-size: 1em;
	text-align: center;
	max-width: 60%;
	margin: 10px auto 0 auto;
}
.btn-top-bar{
 	position: absolute;
	top:3px;
}
.btn-bottom-bar{
 	position: absolute;
	bottom:3px;
}


.left{
	left:5px;
	
}
.right{
	right: 5px;
	
}
.desktop{
	display: none;
}

.menu-side{
	width: 220px;
	position: absolute;
	background: #999;
	height: 100%;
	z-index: 300;
	top:0;
}

#menu-left{

	left: -220px;

	-webkit-transition: left 0.5s; /* For Safari 3.1 to 6.0 */
    transition: left 0.5s;
    
    
	
}
#menu-right{
	right:-220px;
	-webkit-transition: right 0.5s; /* For Safari 3.1 to 6.0 */
    transition: right 0.5s;
}
#main{
	position: relative;
	padding-top: 40px;
	height: 100%;
	left:0;
	right: 0;
    -webkit-transition: all 0.5s; /* For Safari 3.1 to 6.0 */
    transition: all 0.5s;
	/*left: 220px;*/
}


@media(min-width:736px ) {
	body{
		font-size: 16px;
	}
  .desktop{
  	display: inline;
  }

  #timepicer-container{
   width: 75%;
   margin:5px auto 0 auto;
}
}

#main.left-active{
	left: 220px !important;
	
}
#main.right-active{
	left: -220px !important;
	
}
#menu-left.active{
	left:0 !important;
}
#menu-right.active{
	right:0 !important;
}
#menu-side{

	position: absolute;
	right: 5px;
	bottom: 50px;
}
</style>
</head>
<body>
	<div id="menu-left"  class="menu-side">
		
	</div>
	
	
	<div id="main">
	<div id="menu-top">
	<button id="btn-events" class="btn btn-top-bar btn-primary left"><i class="fa fa-flag"></i> <span class="desktop">Events</span></button>
		<h1>{{$map->name}}</h1>
	<button id="btn-places" class="btn btn-top-bar btn-primary right"><i class="fa fa-thumb-tack"></i> <span class="desktop">Places</span></button>
	</div>
	<div id="map" style="width: 100%; height:100%;"></div>

	<div id="menu-side">
		<button class="btn btn-default"><i class="fa fa-reply"></i></button>
	</div>

	<div id="menu-bottom">
	<button class="btn btn-bottom-bar btn-primary left"><span class="glyphicon glyphicon glyphicon-chevron-left" aria-hidden="true"></span> <span class="desktop">Previous</span></button>
		 <div id="timepicer-container" >
		<input type="text"  style="width:100%" data-slider-min="0" data-slider-max="23" data-slider-step="1" data-slider-value="[13,13]" id="timeslider">
		</div>
		<button class="btn btn-bottom-bar btn-primary right"><span class="desktop">Next</span> <span class="glyphicon glyphicon glyphicon-chevron-right" aria-hidden="true"></span></button>
	</div>
	</div>
	<div id="menu-right" class="menu-side" >
		
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
	<script src="//maps.googleapis.com/maps/api/js?key=AIzaSyBtik1WlRukGIeooM_iQirsnrj59gDFHV8&v=3&libraries=geometry,places"></script>
	<script src="{{asset('js/bootstrap-slider.min.js')}}"></script>
	<script>
	function pad(n, width, z) {
		z = z || '0';
  		n = n + '';
  		return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
	}
	$(document).on('ready',function(){
		$('#timeslider').slider({
			formatter: function(time) {
				
				return pad(time[0],2)+":00 "+ " - "+pad(time[1],2)+":59 ";
			},
			tooltip:'always'
			
		});

		$("#btn-events").on('click',function(){
			$('#main').toggleClass('left-active');
			$('#menu-left').toggleClass('active');
		});

		$("#btn-places").on('click',function(){
			$('#main').toggleClass('right-active');
			$('#menu-right').toggleClass('active');
		});


		var canvas = document.getElementById('map'),map,options;
		var mapOptions={
		   zoom: {{$map->zoom}},
		   center: new google.maps.LatLng({{$center[0]}},{{$center[1]}}),
		   disableDefaultUI:false
		};
		map = new google.maps.Map(canvas,mapOptions);
		map.setOptions({{$options}});
	});
	</script>
</body>
</html>