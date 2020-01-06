var Map={};
function strip_tags(input, allowed) {
  allowed = (((allowed || '') + '')
    .toLowerCase()
    .match(/<[a-z][a-z0-9]*>/g) || [])
    .join(''); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
  var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
    commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
  return input.replace(commentsAndPhpTags, '')
    .replace(tags, function($0, $1) {
      return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
    });
}
/******MAP SECTION ***********/
$(document).on('ready',function(){

	Map={
	canvas:null,
	gMap:null,
	searchBox:null,
	markers:[],
	marker:null,
	$center: $('#center'),
		initialize: function(options){

		    this.markers =[],that=this;
			this.canvas = document.getElementById('map');
			this.gMap = new google.maps.Map(this.canvas,options);
			var input = document.getElementById('pac-input');
  			this.gMap.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
  			that.searchBox = new google.maps.places.SearchBox(input);
  			google.maps.event.addListener(that.searchBox, 'places_changed', function(){
  				that.places_changed(that);	
  			});
  		

	  		google.maps.event.addListener(this.gMap, 'bounds_changed', function() {
	    		var bounds = that.gMap.getBounds();
	    		that.searchBox.setBounds(bounds);
			 });
	  		if($("select[name='zoom']").length){
				google.maps.event.addListener(this.gMap,'zoom_changed',function(){
	  				$("select[name='zoom']").val(that.gMap.getZoom());
	  			});
			}
	  		
	  		this.marker = new google.maps.Marker({
			        map: null,
			        position: new google.maps.LatLng(0,0),
			        draggable:true
		    });

		    google.maps.event.addListener(this.marker, 'dragend', function() 
			{
				var position=that.marker.getPosition(),
        pos=position.toString(),

        coords=pos.replace('(','').replace(')',''),
        lat,lng;
        coords=coords.split(',');
       
        lat=coords[0].trim();
        lng=coords[1].trim();
				if(that.$center.length){
   
			    	that.$center.val(lat+','+lng);
				}else{
          
					$("input[name=lat]").val(lat);
					$("input[name=lng]").val(lng);
				}
			});

	  		this.refresh();

			this.refreshControls();

  		},
  		refresh: function(){
  			var center="";

  			if(this.$center.length){
  			var txtcenter=this.$center.val().split(',');
	  			if(txtcenter.length){
	  				center=new google.maps.LatLng(txtcenter[0],txtcenter[1]);	  				
	  			}
	  		}else{
	  			   center=new google.maps.LatLng($("input[name=lat]").val(),$("input[name=lng]").val());
	  		}

	  		this.marker.setPosition(center);
		    this.gMap.setCenter(center);
		    if($("select[name=zoom]").length){
		    	that.gMap.setZoom(parseInt($("select[name=zoom]").val(),10));
		    }else{
		    	that.gMap.setZoom(17);
		    }
	  		
  		},
  		places_changed: function(that){
		    var places = that.searchBox.getPlaces(),place;
		    if (places.length == 0) {
		      return;
		    }
		    place=places[0];
        
		    var bounds = new google.maps.LatLngBounds();
		    this.marker.setPosition(place.geometry.location);
        var lng=place.geometry.location.lng();
		    if(this.$center.length){
		    	this.$center.val(place.geometry.location.lat()+','+place.geometry.location.lng());
		    }else{
		    	$("input[name=lat]").val(place.geometry.location.lat());
				$("input[name=lng]").val(lng);
		    }
		    
		    bounds.extend(place.geometry.location);
		    that.gMap.fitBounds(bounds);
  		},
  		refreshControls: function(){
  			var mapOptions={},
  			zoomControl=$("#zoom_ctrl_pos").val(),
  			panControl=$("#pan_ctrl_pos").val(),
  			mapTypeControl=$("#maptype_ctrl_pos").val(),
  			streetViewControl=$("#streetview_ctrl_pos").val();

  			mapOptions.zoomControl=zoomControl!="";
  			mapOptions.panControl=panControl!="";
  			mapOptions.mapTypeControl=mapTypeControl!="";
  			mapOptions.streetViewControl=streetViewControl!="";
  			mapOptions.zoomControlOptions={
  				position:zoomControl
  			};
  			mapOptions.panControlOptions={
  				position:panControl
  			};
  			mapOptions.mapTypeControlOptionsOptions={
  				position:mapTypeControl
  			};
  			mapOptions.streetViewControlOptions={
  				position:streetViewControl
  			};

			this.gMap.setOptions(mapOptions);
  		},
  		setMapInfo:function(mapInfo){
  			if(!mapInfo) return;
  				var mapOptions={},
  			zoomControl=mapInfo.zoom_ctrl_pos
  			panControl=mapInfo.pan_ctrl_pos,
  			mapTypeControl=mapInfo.maptype_ctrl_pos,
  			streetViewControl=mapInfo.streetview_ctrl_pos;

  			mapOptions.zoomControl=zoomControl!="";
  			mapOptions.panControl=panControl!="";
  			mapOptions.mapTypeControl=mapTypeControl!="";
  			mapOptions.streetViewControl=streetViewControl!="";
  			mapOptions.zoomControlOptions={
  				position:zoomControl
  			};
  			mapOptions.panControlOptions={
  				position:panControl
  			};
  			mapOptions.mapTypeControlOptionsOptions={
  				position:mapTypeControl
  			};
  			mapOptions.streetViewControlOptions={
  				position:streetViewControl
  			};
  			var coords=mapInfo.center.split(",");
  			var position=new google.maps.LatLng(parseFloat(coords[0], 10), parseFloat(coords[1], 10));
  			this.marker.setPosition(position);
  			if(!this.marker.getMap()){
  				this.marker.setMap(this.gMap);
  			}
  			mapOptions.center= position;
			this.gMap.setOptions(mapOptions);

  		},
      showMarker: function(){
        this.marker.setMap(this.gMap);
      },
      clearMarkers:function(){
         $.each(this.markers,function(i,place){
            place.setMap(null);
         });
         this.markers=[];
      },
      showPlaces:function(places){
        this.clearMarkers();
          var pinColor = "3399FF";
          var self=this;
          var pinImage = new google.maps.MarkerImage(root+"/img/marker.png",
              new google.maps.Size(21, 34),
              new google.maps.Point(0,0),
              new google.maps.Point(5, 34));
 
         $.each(places,function(index,place){
          var marker=new google.maps.Marker({
                  map: this.gMap,
                  position: new google.maps.LatLng(place.lat,place.lng),
                  draggable:false,
                  icon: pinImage
                 
            });
            this.markers.push(marker);
            var infowindow = new google.maps.InfoWindow({
                content: '<div>'+strip_tags(place.title)+'</div>'
            });

           google.maps.event.addListener(marker, 'click', function() {
              infowindow.open(self.gMap,marker);
            });

         }.bind(this));
        
      },
  		initializeFormEvents: function(){
  			var that=this;
  			$("select[name=zoom]").change(function(){
  				that.gMap.setZoom(parseInt($(this).val(),10));
  			});
  			if(this.$center.length){
  				this.$center.on('change',function(){
  					that.refresh();
  				});
  			}else{
  				$("input[name=lat],input[name=lng]").on('change',function(){
  					that.refresh();
  				});
  			}
  			$("#zoom_ctrl_pos,#maptype_ctrl_pos,#pan_ctrl_pos,#pan_ctrl_pos,#streetview_ctrl_pos").on('change',function(){
  				that.refreshControls();
  			})
  		}
  	};
  	if($("#map").length){
		var mapOptions={
			zoom: 15,
		   center: new google.maps.LatLng(-34.397, 150.644),
		   disableDefaultUI:false
		};
		
	 	Map.initialize(mapOptions);

	 	Map.initializeFormEvents();
	}
});

/******END MAP SECTION ***********/