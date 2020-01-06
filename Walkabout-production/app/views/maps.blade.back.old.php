<!DOCTYPE html><html>
<head>
	<title>Walkabout</title><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=device-width,initial-scale=1" />
	<link rel="stylesheet" type="text/css" href="{{asset('css/reset.css')}}" />
	<!--<link rel="stylesheet" type="text/css" href="{{asset('css/fonts.css')}}" />
	<link rel="stylesheet" type="text/css" href="{{asset('css/ionicons.min.css')}}" />-->
	<!--<link rel="stylesheet" type="text/css" href="{{asset('css/design.css')}}" />-->
	<!--<link rel="stylesheet" type="text/css" href="{{asset('css/responsive.css')}}" />-->
	<base target="_blank">
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-53675318-1', 'auto');
		ga('send', 'pageview');
	</script>
</head>
<body>
	<div id="content">
	<nav id="left-menu">
		<ul class="events">
			<li class="ico catEvent allEvents">
				<input type="checkbox" id="allEvents" name="categories" value="tevent" checked>
				<label for="allEvents">
					<span> All Events </span>
					<img src="{{asset('img/all-events-icon.svg')}}" alt="All Events">
				</label>
			</li>
		</ul>
	</nav>
	<div id="main">
		<div id="map-canvas"></div>
		<div id="timebar">
			<input type="range" min="0" max="23" step="1" id="timeslider">
			<div class="date">
				<span class="ion-chevron-left" id="arrow-left"></span>
				<output></output>
				<span class="ion-chevron-right" id="arrow-right"></span>
			</div>
		</div>
		<nav id="main-controls">
			<button type="button" id="filter-events-button">Filter Events</button><button type="button" id="find-me-button">Find Me</button><button type="button" id="entire-map-button">Entire Map</button><button type="button" id="filter-places-button">Filter Places</button>		</nav>
		<div id="overlay"></div>
	</div>
	<nav id="right-menu">
		<ul class="places">
			<li class="ico catPlace allPlaces">
				<input type="checkbox" id="allPlaces" name="categories" value="tplace" checked>
				<label for="allPlaces">
					<span> All Places </span>
					<img src="{{asset('img/all-places-icon.svg')}}" alt="All Places">
				</label>
			</li>
		</ul>
	</nav>
</div>
<script type="text/javascript">
	var wroot = "{{url('/')}}/";
	var params = {"hash":"d3d9446802a44259755d38e6d163e820","userid":1,"groupid":1};
</script>
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBtik1WlRukGIeooM_iQirsnrj59gDFHV8&amp;v=3&amp;libraries=geometry"></script>
	<script type="text/javascript" src="{{asset('js/lib/jquery-2.1.1.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('js/lib/rangeslider-0.3.1.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('js/lib/moment.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('js/jquery-map/min/jquery.ui.map.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('js/jquery-map/min/jquery.ui.map.overlays.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('js/jquery-map/jquery.ui.map.extensions.js')}}"></script>
	<script type="text/javascript" src="{{asset('js/map.js')}}"></script>
	<script>
		var client_ip = '';
		jQuery.ajax({
			url: "//ip-api.com/json",
			dataType: 'json',
			done: function(json) {
				client_ip = json.query
			}
		});
	</script>
</body>
</html>