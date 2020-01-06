$(document).ready(function($) {
	console.log(loc);
	$('#use_map').one('click', function() {
		$('#use_map').attr('disabled', 'disabled');
		$('#geo_map').gmap({
			'center': (loc ? ('' + loc.lat + ',' + loc.lng) : '33.7733502,-84.3645822'),
			'maxZoom': 18,
			'minZoom': 5,
			'zoomControl': true,
			'zoom': 10,
			'zoomControlOptions': {
				'position': google.maps.ControlPosition.LEFT_BOTTOM,
				'style': google.maps.ZoomControlStyle.SMALL
			},

			'disableDefaultUI': true,

			callback: function(map) {

				var self = this;

				$(map).addEventListener('zoom_changed', function(ev) {
					$('#zoom').val(map.getZoom());
				});

				$('#lat').on('input', function() {
					loc.lat = this.value;
					changeLocation(self, loc);
				});

				$('#lng').on('input', function() {
					loc.lng = this.value;
					changeLocation(self, loc);
				});

				self.set('findByLocation', function(location, marker) {
					self.search({'location': location}, function(results, status) {
						if ( status === 'OK' ) {
							$('#location').val(results[0].formatted_address);
							$('#lat').val(results[0].geometry.location.lat());
							$('#lng').val(results[0].geometry.location.lng());
							console.log(JSON.stringify(results));
						}
					});
				});

				self.set('findByAddress', function(address, marker) {
					self.search({'address': address}, function(results, status) {
					
						if ( status === 'OK' ) {
							marker = marker || self.get('markers').pin;
							// marker.set('position', results[0].geometry.location);
							if (marker)
								marker.setPosition(results[0].geometry.location);
							self.get('map').panTo(results[0].geometry.location);

							$('#location').val(results[0].formatted_address);
							$('#lat').val(results[0].geometry.location.lat());
							$('#lng').val(results[0].geometry.location.lng());
							
							console.log(JSON.stringify(results));
						}
					});
				});

				self.addMarker({
						'id': 'pin',
						'position': ('' + loc.lat + ',' + loc.lng),
						'draggable': true, 
						'bounds': false
					}, function(map, marker) {
							self.get('findByLocation')(marker.getPosition(), marker);
						}
					).dragend(
						function(event) {
							self.get('findByLocation')(event.latLng, this);
						}
					).click(
						function() {}
					);

				$()


				$('#search_but').on('click', function(ev) {
					ev.preventDefault();

					var expr = $.trim(($('#search_address').val() || ""));
					if (expr.length>0){
						self.get('findByAddress')(expr);
					}
				});
			}
		});
	});
});

function changeLocation(map, loc) {
	var location = new google.maps.LatLng(loc.lat, loc.lng);
	var marker = $('#geo_map').gmap('get', 'markers')['pin'];
	if (marker) {
		marker.setPosition(location);
		$('#geo_map').gmap('option', 'center', location);
		map.get('findByLocation')(marker.getPosition(), marker)
	}
}