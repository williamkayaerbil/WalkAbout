var Instance = {};

$(document).ready(function($) {
	Instance.initialize();
});

Instance.initialize = function() {
	var self = Instance;

	// Set initial states of pans
	$.each($('.ctrlbox input:checkbox'), function(k, chkbox) {
		var $chkbox = $(chkbox);
		self.expandCtrlBox($chkbox.parents(".ctrlbox"), $chkbox.prop('checked'));
	});

	self.initMap();
	self.bindUIEvents();
}

Instance.bindUIEvents = function() {
	var self = Instance;

	// Click to Remove Layer from List
	$(".mlayersTble").on('click', '.rmv-btn', function(ev) {
		var $tr = $(this).parents('tr');
		$tr.fadeOut(300, function(){$tr.remove();});
	});

	// Click to Add Layer To List
	$("#addLayerToList").on('click', function(ev) {
		var n = $('input.mlayer').length + 1,
			$elmt = $("#map_layer option:selected");

		$(".mlayersTble tr").last().before(
				'<tr>'
				+ '<td><input type="hidden" name="mlayers[]" id="mlayer' + n + '" value="' + $elmt.val() + '" class="mlayer">' + n + '</td>'
				+ '<td>' + $elmt.data('name') + '</td>'
				+ '<td>' + $elmt.data('description') + '</td>'
				+ '<td><button type="button" class="rmv-btn btn btn-default btn-sm" title="Remove Layer from list">'
				+ '<span class="glyphicon glyphicon-remove"></span></button></td>'
				+ '</tr>');

	});
}

Instance.initMap = function() {
	var self = Instance;

	// Initialize the map
	$('#instance_geo_map').gmap({ 
			'center': mapOptions.center,
			'maxZoom': 18,
			'minZoom': 5,
			'zoom': parseInt(mapOptions.zoom),
			'mapTypeControl': (parseInt(mapOptions.maptype_ctrl)==1),
			'mapTypeControlOptions': {
			  'position': parseInt(mapOptions.maptype_ctrl_pos)
			},
			'panControl': (parseInt(mapOptions.pan_ctrl)==1),
			'panControlOptions': {
			  'position': parseInt(mapOptions.pan_ctrl_pos)
			},
			'zoomControl': (parseInt(mapOptions.zoom_ctrl)==1),
			'zoomControlOptions': {
			  'position': parseInt(mapOptions.zoom_ctrl_pos)
			},
			'streetViewControl': (parseInt(mapOptions.streetview_ctrl)==1),
			'streetViewControlOptions': {
			  'position': parseInt(mapOptions.streetview_ctrl_pos)
			},

			'disableDefaultUI':true,

			callback: function(map) {

				var mself = this;

				$(map).addEventListener('zoom_changed', function(ev) {
					$('#zoom').val(map.getZoom());
				});

				// Map pin location changed callback
				mself.set('locationChanged', function(location, marker) {
					$("#center").val(location.lat() + ',' + location.lng());
				});

				// Sets the pin location on the map 
				mself.set('setULocation', function(location, marker) {
					if (!self.validate(location)) return;
					marker = marker || mself.get('markers').pin;
					if (marker)
						marker.setPosition(location);

					map.panTo(location);
				});

				// Add a pin on the map
				mself.addMarker({
						'id': 'pin',
						'position': mapOptions.center, 
						'draggable': true, 
						'bounds': false}, 
						function(map, marker) {
							mself.get('locationChanged')(marker.getPosition(), marker);
						}
					).dragend( 
						function(event) {
							// map.setCenter(event.latLng);
							map.panTo(event.latLng);
							mself.get('locationChanged')(event.latLng, this);
						}
					).click( 
						function() {}
					);


					// Bind some map-related events listeners

					$('#previewCenter').on('click', function(ev) {
						ev.preventDefault();
						var value = $.trim($(this).parents('.input-group').find('input').val()),
							 location = null;

						if (value.length==0) return;

						var coords = value.split(',');

						if (coords.length<2) return;

						location = new google.maps.LatLng(parseFloat(coords[0]), parseFloat(coords[1]));
						mself.get('setULocation')(location);
					});

					$('#previewZoom').on('click', function(ev) {
						ev.preventDefault();
						var value = $.trim($(this).parents('.input-group').find('input').val()),
							 location = null;

						if (value.length==0) return;
						map.setZoom(parseInt(value));
					});

					$('.form-layout').on('click', '.ctrlbox input:checkbox', function(ev) {
						var $this = $(this),
							$ctrlbox = $this.parents('.ctrlbox'),
							$pos = $ctrlbox.find('.ctrlPosition select'),
							checked = $this.prop("checked");

						self.expandCtrlBox($this.parents(".ctrlbox"), checked);

						var key = $ctrlbox.data("key"),
							 options = checked;
						// map.setOptions(key, options);

				// if (options) {
				// 	this.options[key] = options;
						map.set(key, options);
				// 	return this;
				// }
				// return this.options[key];

						// if (checked) {
						// 	key += "Options";
						// 	options = {
						// 		'position': parseInt($pos.val())
						// 	};
						// 	map.setptions(key, options);
						// }


					});

			}
	});
}

/**
*	Expands/Collapses Controls fields
*/
Instance.expandCtrlBox = function(ctrlbox, expand) {
	if (!ctrlbox) return;

	ctrlbox = $(ctrlbox);
	var $ctrlPos = ctrlbox.find(".ctrlPosition");

	if (expand){
		$ctrlPos.show(200);
		ctrlbox.removeClass('collapsed');
	}else{
		$ctrlPos.hide();
		ctrlbox.addClass('collapsed');
	}

}

/**
*	Checks if value is a location
*/
Instance.validate = function(value) {
	return true;
}