function pad(n, width, z) {
    z = z || '0';
      n = n + '';
      return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
  
}
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


(function(window, $) {

var infowindow=null;

var generateUid = function (separator) {


    var delim = separator || "-";

    function S4() {
        return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
    }

    return (S4() + S4() + delim + S4() + delim + S4() + delim + S4() + delim + S4() + S4() + S4());
};


  
  var dataMap={
    map:null,
    markers:[]
  };
  var isDragging=false;
  $calendar=null;
  var Walkabout = {};
  Walkabout.interactions=[];
  Walkabout.time=new Date().getTime();
  $(document).ready(function() {


    $('.navbar,.back-controls,#btn-events,#btn-places,#bottom-bar').on('touchstart', function (event) {
            dataMap.map.setOptions({draggable: false});
    });
        $('.navbar,.back-controls,#btn-events,#btn-places,#bottom-bar').on('touchend', function (event) {
            dataMap.map.setOptions({draggable: true});
    });
   

  navigator.geolocation.watchPosition(function(pos){
    position=pos;
  });

    Walkabout.params = params;
    Walkabout.initialize();
      var mySwiper = new Swiper('#swiper-help',{
        pagination: '#pagination-help',
        paginationClickable: true
      }); 
      $(".btn-yes").on('click',function(){
        $("#overlay-first-visitor").hide();
        $("#help").css('visibility','hidden');
      });
      
      $("#content-date").on('click',function(){
          $calendar=$(".gldp-flatwhite").toggleClass('calendar-hidden');
          if(!$calendar.hasClass('calendar-hidden')){
            $("#overlay-calendar").show();  
          }else{
            $("#overlay-calendar").hide();  
          }
        
          
      });
      $("#overlay-calendar").on('click',function(){
          $(".gldp-flatwhite").toggleClass('calendar-hidden');
          $(this).hide();
      });
      $("#search-events").on('keyup',function(){
         Walkabout.searchCategories($(this),$('.events'));
      });
      $("#search-places").on('keyup',function(){
         Walkabout.searchCategories($(this),$('.places'));
      });
      $("#overlay").on('click',function(){
         Walkabout.refreshDisplay();
      });
      $(document).on('click',function(){
          Walkabout.time=new Date().getTime();
          $("#bottom-bar").show();
          if(Walkabout.timepickerHidden){
            $("#timepicker-container-vertical").show();  
            $(".back-controls").show();  
            Walkabout.timepickerHidden=false;
          }
          
      });
      
      $(document).on('click','a',function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        var action = $(this).attr('id');
        if(!action=='btn-places' || action == 'btn-burger' || action == 'btn-events') {return;}
        
        if($(this).closest('.markerInfo').length) {action ='info-bubble-item';}
        if($(this).closest('#menu-burger').length){ action ='menu-burger-list-item';}
        console.log(Walkabout.registerInteraction);
         Walkabout.registerInteraction(action,0,"click",function(){
         });
         if(href!='#' && href !=''){
          window.open(href);
        }
      });
     
      $("#new-window-close").on('click',function(){
          $("#new-window").hide();
          $("#new-window-iframe").attr("src","");  
      });

      $(document)
        .mousedown(function() {
            $(window).mousemove(function() {
                isDragging = true;
                $(window).unbind("mousemove");
            });
        })
        .mouseup(function() {
            var wasDragging = isDragging;
            isDragging = false;
            $(window).unbind("mousemove");
            
        });

      /*$(document).on('click','a',function(e){
          e.preventDefault();
          var href=$(this).attr("href");
         if(href.replace(wroot).length<href.length){
            $("#new-window-iframe").attr("src",href);  
            $("#new-window").show();
          }else{
            location.href=href;
          }
      });*/
  });
  Walkabout.searchCategories=function(input,ul){
    var li = ul.find('li');
    var value = input.val();
    if(value=='') {
      li.show();
      li.find('input').prop('checked',false);
      li.eq(0).find('input').prop('checked',true); 
      return;
    }
    $(li).each(function(i,item){
        if(i===0)  {
          $(this).find('input').prop('checked',false); 
          return;
        }
        if($(this).text().search(new RegExp(value,'i'))>-1){
          $(this).show();
          $(this).find('input').prop('checked',true);
        }else{
          $(this).hide();
          $(this).find('input').prop('checked',false);
        }
    });
  }

  Walkabout.showHelp=function(){
     $("#overlay-first-visitor").show();
     $("#help").css('visibility','visible');
  };
  Walkabout.showFirstPage=function(){
    return;
    var self=this;
    /*try{
      localStorage.first_time=1;
      localStorage.shareLocation=1;
    }catch(e){

    }
    return;*/
   
      try{
          if(!localStorage.first_time){
           var mySwiper = new Swiper('#swiper-first-visitor',{
            pagination: '.pagination',
            paginationClickable: true
          });
          var overlay=$("#overlay-first-visitor").show();
          var first=$("#first-visitor").css('visibility','visible');
          $("#read-terms").on('click',function(e){
              e.preventDefault();
              mySwiper.swipeNext();
          });
          $("#btn-accept").on('click',function(){
              first.hide();
              overlay.hide();
              overlay=null;
              first=null;
              localStorage.first_time=1;
              localStorage.shareLocation=1;
              self.showHelp();
          });
          $("#btn-cancel").on('click',function(){
              first.hide();
              overlay.hide();
              overlay=null;
              first=null;
              localStorage.first_time=1;
              localStorage.shareLocation=0;
              self.showHelp();
          });
        }
      }catch(e){}
  };
  Walkabout.api = {
    data: wroot + "api/data",
    interactions: wroot+'api/interactions'
  };
  Walkabout.categories={};
  Walkabout.defaultMapOpts = {
    center: '33.7733502,-84.3645822',
    zoom: 9,
    zoom_ctrl: 0,
    zoom_ctrl_pos: 5,
    maptype_ctrl: 0,
    maptype_ctrl_pos: 5,
    pan_ctrl: 0,
    pan_ctrl_pos: 5,
    streetview_ctrl: 0,
    streetview_ctrl_pos: 5
  }

  Walkabout.tags = {
    places:{},
    events:{}
  };

  Walkabout.interval = {
    start: {
      date: null
    },
    current: {
      from: null,
      to: null,
      date: null
    },
    end: {
      date: null
    }
  };

  Walkabout.filterOptions = {
    "tags": {
      places:{},
      events:{}
    },
    "dateInterval": {
      "from": new Date('2011-12-01T01:00:00'),
      "to": new Date('2030-12-31T12:00:00')
    }
  };
  Walkabout.client_ip='';
  Walkabout.entireMapOptions = {};
  var currentLocation=null,
      allowedLoaction=true,
      position=null;
  Walkabout.sendInteractions=function(){
    var self=Walkabout;
    if(!infoMap.map.click_event) return;
    var lat='',lng='';
    if(position){
      lat=position.coords.latitude;
      lng= position.coords.longitude;
    }
        $.ajax({
          url:self.api.interactions,
          dataType:'json',
          type:"POST",
          data:  {interactions:[{
            "client_ip": self.client_ip,
            "UID": localStorage.UID,
            latitude:lat,
            longitude: lng,
            map_id:Walkabout.mapId,
            action:'Navigate',
            object:null,
            object_id: 0
          }]},
        }).done(function(res){
          console.log(res);
        });
  };
   Walkabout.timepickerHidden=false;
  Walkabout.hideElements=function(){ 
    var time=new Date().getTime();
    var seconds=(time-this.time)/1000;
    var timePicker=null;
    if(seconds>5 && !isDragging){
      $("#bottom-bar").hide();
      timePicker=$("#timepicker-container-vertical");
      if(timePicker.is(":visible")){
        timePicker.hide();  
        $(".back-controls").hide();  
        Walkabout.timepickerHidden=true;
      }
      this.time=new Date().getTime();
      timePicker=null;
    }
    

  }
  Walkabout.initialize = function() {
    var self = Walkabout;
    try{
         localStorage.UID=localStorage.UID || generateUid(); //UID 
    }catch(e){

    }
   
    

    self.initializeMap();
    self.bindUIEvents();
  };
  Walkabout.indexCategories=function(categories){
    var cats={};
    $.each(categories, function(index,cat){
      cats["c_"+cat.id]=cat;
    });
    Walkabout.categories=cats;
  };

  Walkabout.getLastDate=function(places){
    var lastDate=new Date("01/01/1990");
   
      for(var i=0; i<places.length; i++){
      if(places[i] && places[i].events && places[i].events.length){

        for(var j=0; j<places[i].events.length; j++){
          var date=moment(places[i].events[j].end,'YYYY-MM-DD HH:mm:ss'),
          currentDate=new Date(date.format());
          if(lastDate < currentDate){
            lastDate=currentDate;
          }
        }
    
      }
    }
    return lastDate;
  }
  Walkabout.getFirstDate=function(places){
    var firstDate=new Date("01/01/2990");
      var first=false;
      for(var i=0; i<places.length; i++){
      if(places[i].events && places[i].events.length){
        
        for(var j=0; j<places[i].events.length; j++){
          var date=moment(places[i].events[j].start,'YYYY-MM-DD HH:mm:ss'),
          currentDate=new Date(date.format());
          if(firstDate > currentDate ){
            firstDate=currentDate;
              first=true;
          }   
        }
      
      }
    }
   
    if(!first) return false;
    return firstDate;
    
  }
  Walkabout.renderMenuBurger=function(data){
    if(!data.map.menu_options)return;
      
      try{
        var menu=JSON.parse(data.map.menu_options),
        html=[];
        $.each(menu,function(index,item){
          html.push('<li><a href="'+item.url+'">'+item.name+'</a></li>')
        });
      $('#menu-burger').html(html.join(''));
      }catch(e){
        console.log(e);
      }
      

  };
  Walkabout.renderLogos=function(map){
    if(!map) return;
    
    if(map.menu_logo){
       $("#logo-burger").attr('src',wroot+'/img/map_logos/'+map.menu_logo);
    }
    if(map.menu_logo_url){
       $("#logo-burger-url").attr('href',map.menu_logo_url);
    }
    if(map.place_logo){
      $("#side-places .logo-container a").append('<img src="'+wroot+'/img/map_logos/'+map.place_logo+'">');
    }
    if(map.place_logo_url){
      $("#side-places .logo-container a").attr('href',map.place_logo_url);
    }

    if(map.event_logo){
      $("#side-events .logo-container a").append('<img src="'+wroot+'/img/map_logos/'+map.event_logo+'">');
    }

    if(map.event_logo_url){
      $("#side-events .logo-container a").attr('href',map.event_logo_url);
    }
     if(map.header_logo){
      $("#logo-header").attr('src',wroot+'/img/map_logos/'+map.header_logo); 
    }
    if(map.header_logo_url){
      $("#logo-header-url").attr('href',map.header_logo_url);       
    }
  }

Walkabout.getSpecialDates=function(places){
  var dates={};
     $.each(places,function(index,place){ 
       $.each(place.events,function(i,event){
    
        for (var m = moment(event.start); m.isBefore(event.end); m.add(1, 'days')) {
            dates[m.format('YYYY-MM-DD')]=new Date(m.format());
        }
    
       });      
     });
    var specialDates=[];
    $.each(dates,function(index,date){
      specialDates.push({
        "date":date
      });
    });
    return specialDates;
  };
  var calendar=null;
  Walkabout.mapId=-1;
  var infoMap=null;
  Walkabout.initializeCalendar=function(data){
    var self=this;
    
      var specialDates=this.getSpecialDates(data.places);
      calendar=$('#glcalendar').glDatePicker({
        showAlways: true,
        cssName: 'flatwhite',
        specialDates: specialDates,
        selectedDate: new Date(moment(self.interval.current.date).format()),
        onClick: function(target, cell, date, data){
          self.interval.current.date = moment(self.interval.current.date).date(date.getDate()).month(date.getMonth()).year(date.getFullYear());
          self.interval.current.from = moment(self.interval.current.from ).date(date.getDate()).month(date.getMonth()).year(date.getFullYear());
          self.interval.current.to = moment(self.interval.current.to ).date(date.getDate()).month(date.getMonth()).year(date.getFullYear());
          self.refreshSlider();
        }
      });
        calendar = $('#glcalendar').glDatePicker(true);
       $(".gldp-flatwhite").addClass('calendar-hidden');
  };
  Walkabout.initializeMap = function() {
    var self = Walkabout,loaders=[],images={};

    // Api request parameters
    var formData = {
        "from": "2013-12-01T01:00:00",
        "to": "2014-12-31T00:00:00",
        "params": self.params
      };

      $.ajax({
        'type':'GET',
        'dataType':'JSON',
        'url': self.api.data+'/'+self.params.hash
      }).done(
      function(data) {
        infoMap=data;
        Walkabout.mapId=data.map.id;


      window.setInterval(function(){
        self.sendInteractions();
      },data.map.navigation_time*1000); //5 seconds to send user data
      window.setInterval(function(){
        self.hideElements();
      },1000); //1 second




        self.showFirstPage();
        self.renderMenuBurger(data);
        self.renderLogos(data.map);
        self.client_ip=data.client;
        self.displayCategories(data.categories);
        self.time=new Date().getTime();
        var dMap = null;

        if (typeof data.map === "undefined" || !data.map) {
          dMap = self.defaultMapOpts;
        } else {
          dMap = data.map;
        }
        
        self.setTimeInterval(data.places);
        self.initializeCalendar(data);
        //self.showMapLogo(dMap);
        self.entireMapOptions = dMap;
        self.indexCategories(data.categories);
        var urlMarkers=self.getURLMarkers(data.places);
          $.each(urlMarkers,function(id,url){
            var deferred = $.Deferred();
            images[id]=new Image();
            images[id].src=url;
            images[id].width='100';
            images[id].height='100';
            images[id].onload = function() {
                  deferred.resolve();

            };
            images[id].onerror = function() {
                  deferred.resolve();

            };
            loaders.push(deferred.promise());
          });
          var myStyles =[
              {
                  featureType: "poi",
                  elementType: "labels",
                  stylers: [
                        { visibility: "off" }
                  ]
              }
          ];
          dataMap.map= new google.maps.Map(document.getElementById('map-canvas'),{ styles: myStyles,   disableDoubleClickZoom: true});
          google.maps.event.addListener(dataMap.map, 'dblclick', function(e) { 
             e.stop();
             return false;
          });

          dataMap.clientZoom=data.map.client_zoom;
          dataMap.map.setOptions({

          mapTypeControl: dMap.maptype_ctrl,
          mapTypeControlOptions: {
            position: dMap.maptype_ctrl_pos
          },
          panControl: dMap.pan_ctrl,
          panControlOptions: {
            position: dMap.pan_ctrl_pos
          },
          zoomControl: dMap.zoom_ctrl,
          zoomControlOptions: {
            position: dMap.zoom_ctrl_pos
          },
          streetViewControl: dMap.streetview_ctrl,
          streetViewControlOptions: {
            position: dMap.streetview_ctrl_pos
          }
          });
          self.entireMap({use_location:false});
          google.maps.event.addListener(dataMap.map, 'click', function() {
            if(infowindow){
                  infowindow.close();
              }
            }    
          ); 

          self.loadKML(data, dataMap.map);
          self.loadJSON(data,dataMap.map);
          google.maps.event.addListenerOnce(dataMap.map, 'bounds_changed', function() {
            self.entireMap({use_location:true});
            });
          $.when.apply(null, loaders).then(function() {

          }).always(function(){
            
            self.initializeTimeBar(data.map);
            //
            self.plotPlaces(data.places,images);
            self.filterOptions.tags = $.extend({},self.tags);
      
            self.filter(self.filterOptions.tags, self.filterOptions.dateInterval);
            self.hideLoading();
          });
      });
  };

  Walkabout.hideLoading=function(){
    $("#loading").hide();
  };
  Walkabout.bindUIEvents = function() {
    var self = Walkabout;
    $('#entire-map-button').on('click', function(e) {
      e.preventDefault();
      self.entireMap({use_location:true});
    });
    
    $('#previous').on('click', function(e) {
      e.preventDefault();
      self.previous();
    });
    $('#next').on('click', function(e) {
      e.preventDefault();
      self.next();
    });

    $('.events').on('change', 'input',function() {
      if($(this).attr('id')=="allEvents"){
        $('.events input').not("#allEvents").each(function(index, checkbox) {
          $(checkbox).prop('checked', false);
        });
      }else{
        $allEvents.prop('checked', false);  
      }
    });

    $('.places').on('click','input', function() {
      if($(this).attr('id')=="allPlaces"){
        $('ul.places input').not("#allPlaces").each(function(index, checkbox) {
          $(checkbox).prop('checked', false);
        });
      }else{
        $allPlaces.prop('checked', false);  
      }
    });

    $('ul').on('click','input', function() {
      self.refreshDisplay();
    });
  };

   var $allEvents=$('#allEvents'),
    $allPlaces=$('#allPlaces'),
    $events=null; 
    $places=null; 
  Walkabout.refreshDisplay = function() {
    var self = Walkabout;

  
    self.filterOptions.tags.places={};
    self.filterOptions.tags.events={};
    if ($allEvents.prop('checked')) {
      self.filterOptions.tags.events = $.extend({},self.tags.events);
    } else {
      $events.each(function(i, chck) {
        if(chck.checked){
          self.filterOptions.tags.events[$(chck).val()]=true;
        }
      });
    }
      
    if ($allPlaces.prop('checked')) {
      self.filterOptions.tags.places = $.extend({},self.tags.places);
    } else {
      
      $places.each(function(i, chck) {
        if(chck.checked){
          self.filterOptions.tags.places[$(chck).val()]=true;
        }
      });
    }
  
    self.filter(self.filterOptions.tags, self.filterOptions.dateInterval);

  };
  Walkabout.displayCategories = function(categories) {
    var self = Walkabout;
    var htmlEvents=[],htmlPlaces=[];
    if (categories) {
      $.each(categories, function(catId, category) {
        if (category.type == "1") {
          self.tags.events[category.id]=true;
        }else{
          self.tags.places[category.id]=true;
        }
        

        var CategoryImages = (category.image) ? category.image : '',
          catIcon;

          
        if (category.type == "1") {
          catIcon = (CategoryImages) ? '<img src="' + wroot + "img/categories/" + CategoryImages + '" alt="Events: ' + category.title + '">' : '';
          
            htmlEvents.push(
              '<li class="ico catEvent">\
                <input type="checkbox" id="c_' + category.id + '" name="categories" value="' + category.id + '">\
                <label class="tag" for="c_' + category.id + '">\
                  <span>' + category.title + '</span>'
                  +catIcon+
                  '<span class="border" style="background:'+ (category.color?category.color:'#f7f7f7') +'">\
                </label>\
              </li>');
          
        } else {
          catIcon = (CategoryImages) ? '<img src="' + wroot +  "img/categories/" + CategoryImages + '" alt="Places: ' + category.title + '">' : '';
        
            htmlPlaces.push(
              '<li class="ico catPlace">\
               <input type="checkbox" id="c_' + category.id + '" name="categories" value="' + category.id + '">\
                <label class="tag"  for="c_' + category.id + '">\
                  <span>' + category.title + '</span>'+
                  catIcon+
                  '<span class="border" style="background:'+ (category.color?category.color:'#f7f7f7') +'">\
                </label>\
              </li>'
          ) ;
        }
      });
      $('ul.events').append(htmlEvents.join(""));
      $('ul.places').append(htmlPlaces.join(""));
      $events=$('ul.events input').not("#allEvents"),
      $places=$('ul.places input').not("#allPlaces");
    }
  };

var canvas2=document.createElement('canvas');
function createMarker(width, height,thumbImg,cat) {
      canvas.globalAlpha = 1;
      canvas.width = width;
      canvas.height = height;
      context = canvas.getContext("2d");
      var pixelRatio = 1;
      //pixelRatio*=2;
      width=width/pixelRatio;  
      height=height/pixelRatio;
      context.setTransform(pixelRatio,0,0,pixelRatio,0,0);
      context.clearRect(0,0,width,height);

      if(thumbImg){
           try{
            //context.putImageData(map,0,0, parseInt(width*0.5-width*0.3,10)+1, parseInt(height*0.5-height*0.3,10)+1,parseInt(width*0.6,10),parseInt(height*0.6,10));
            context.drawImage(thumbImg, parseInt(width*0.5-width*0.3,10)+1, parseInt(height*0.5-height*0.3,10)+1,parseInt(width*0.6,10),parseInt(height*0.6,10)); //Fail with SVG
          }catch(e){
            console.log(e);
          }
        }

        context.globalCompositeOperation = "destination-over";  
      if(cat.color && cat.color=="#000000"){
        context.fillStyle = "#333333";  
      }else{
        context.fillStyle = cat.color;  
      }

      context.beginPath();
        context.arc(width/2+1, height/2+1, width/2-4, 0, Math.PI * 2);
        context.closePath();
            
      context.fill();
        context.lineWidth = 3;
        context.clip();   
        //
      return canvas.toDataURL();
 
}
  

  Walkabout.markers={};

  Walkabout.plotEvents = function(events,place,images) {
    var self = Walkabout;
    if (!dataMap.map || !events) return;
      $.each(events, function(id, oEvt) {
          var catId = oEvt.category_id,
          cat = self.categories["c_"+catId], thumbImg;
            iconUrl = "";
            if(!cat) return;
          if(!self.markers['e_'+cat.id]){
              self.markers['e_'+cat.id]=new google.maps.MarkerImage(createMarker(64,64,images[cat.image],cat),null,null,null,new google.maps.Size(32,32));
          }
          
        var marker=new google.maps.Marker({
              position:  new google.maps.LatLng(place.lat, place.lng),
              map: null,
              date: new Date(moment( oEvt.start).format()),
              end:new Date(moment( oEvt.end).format()),
              cat:cat.id,
              type:2,
              icon: self.markers["e_"+cat.id],
              zIndex: 2
          });
        dataMap.markers.push(marker);
          google.maps.event.addListener(marker, 'click', function() {
            if(infowindow){
                infowindow.close();
              }
        

             infowindow = new google.maps.InfoWindow({
                content: self.buildMarkerInfos(oEvt,place)
             });
            infowindow.open(dataMap.map,marker);
            try{
             if(localStorage.shareLocation && parseInt(localStorage.shareLocation,10) === 1){
                self.registerInteraction("event",oEvt.id,"click");
              }

            }catch(e){

            }
         


          });
      });
  };
  
  Walkabout.getURLMarkers=function(places){
    var catId="",self=Walkabout,urlMarkers={};
      if (places) {
      $.each(places, function(id, place) {
        if(place.events && place.events.length){
          $.each(place.events,function(id,ev){
              catId = ev.category_id,
              cat = self.categories["c_"+catId];
                if(cat){
                  urlMarkers[cat.image]=wroot + 'img/categories/'+cat.image;    
                }
          });
        }
        catId = place.category_id,
          cat = self.categories["c_"+catId];
            if(cat) {
              urlMarkers[cat.image]=wroot +  'img/categories/'+cat.image;  
            }
            
      });
      }
      return urlMarkers;
  };

  Walkabout.plotPlaces = function(places,images) {
    
    var self = Walkabout,thumbImg ;
    if (!dataMap.map || !places) return;
      $.each(places, function(id, oPlce) {
        
        if(oPlce.events && oPlce.events.length){

          self.plotEvents(oPlce.events,oPlce,images);
          //return;
        }
        var catId = oPlce.category_id,
          cat = self.categories["c_"+catId],
            iconUrl = "";
            if(!cat) return;
            if(!self.markers['p_'+cat.id]){
              self.markers['p_'+cat.id]=new google.maps.MarkerImage(createMarker(64,64,images[cat.image],cat),null,null,null,new google.maps.Size(32,32));
          }

        var marker=new google.maps.Marker({
              position:  new google.maps.LatLng(oPlce.lat, oPlce.lng),
              map:null,
              date: new Date(moment(oPlce.start).format()),
              end:new Date(moment(oPlce.end).format()),
              icon: self.markers["p_"+cat.id],
              cat:cat.id,
              type:1,
              permanent:oPlce.permanent,
              zIndex:1
          });
         dataMap.markers.push(marker);
         google.maps.event.addListener(marker, 'click', function() {
            if(infowindow){
                infowindow.close();
              }
          
             infowindow = new google.maps.InfoWindow({
                content: self.buildMarkerInfos(oPlce)
             });
            infowindow.open(dataMap.map,marker);
            try{
              if(localStorage.shareLocation && parseInt(localStorage.shareLocation,10) === 1){
               self.registerInteraction("place",oPlce.id,"click");
              }
            }catch(e){

            }
        
          });
      });

    
  };
  Walkabout.registerInteraction = function(object, object_id, action) {
      var self = Walkabout;
    if(!infoMap.map.click_event) return;
    var lat='',lng='';
    if(position){
      lat=position.coords.latitude;
      lng= position.coords.longitude;
    }
     $.ajax({
          url:self.api.interactions,
          dataType:'json',
          type:"POST",
          data:  {interactions:[{
              "client_ip": self.client_ip,
              "UID": localStorage.UID,
              latitude: lat,
              longitude: lng,
              action: action,
              object: object,
              map_id:Walkabout.mapId,
              object_id: object_id
            }]},
        }).done(function(res){
          console.log(res);
        });
    };
  Walkabout.buildMarkerInfos = function(incident,place) {
    
    var html='';
    html = '<div class="markerInfo infoWindowNoScrollbar">\
            <div>\
              <div class="incident"><h3>' + strip_tags(incident.title) + '</h3></div>\
              <div class="description">';
              
              if(incident.picture){
                var folder="places";
                if(place) folder="events";
                  html+='<a href="'+wroot+'/img/'+folder+'/'+incident.picture+'"><img align="left" src="'+wroot+'/img/'+folder+'/'+incident.picture+'"></a>';
                }

               html+=  incident.description + '</div>';
               if(!incident.permanent){
                  html+='  <div class="footer"><div class="date"> From: ' + strip_tags(Walkabout.formatDate(incident.start)) +' '+moment(incident.start).format('hh:mm a')+ '</div>\
                  <div class="end"> To: ' + strip_tags(Walkabout.formatDate(incident.end) +' '+moment(incident.end).format('hh:mm a'))+ '</div></div>';
               }
              html+= (incident.location? '   <div class="location">' + strip_tags(incident.location) + '</div>':'')+ ' </div>';

          if(incident.facebook){
            html+='<a href="'+incident.facebook+'"><i class="fa fa-facebook social-icon"></i></a>'; 
          }
          if(incident.twitter){
            html+='<a href="'+incident.twitter+'"><i class="fa fa-twitter social-icon"></i></a>';  
          }    
          if(incident.instagram){
            html+='<a href="'+incident.instagram+'"><i class="fa fa-instagram social-icon"></i></a>';  
          }
        html+='</div>';

    return html;
  };
  Walkabout.showMapLogo = function(map) {
    
    if (map.logo) {
      var html = '<a class="logo" target="_blank">' + 
              '<img src="'+wroot+'/img/map_logos/' + map.logo + '" alt="' + map.name + '">' +
            '</a>';
      $('#left-menu, #right-menu').prepend(html);
    }
  };

  Walkabout.loadKML = function(data, map,index) {
    if(!data.map.layers) return;
    for(var i=0; i<data.map.layers.length;i++){
    if(data.map.layers[i] && data.map.layers[i].kml_layer){
        var ctaLayer = new google.maps.KmlLayer({
            url:  encodeURI(wroot+'kml_layers/'+data.map.layers[i].kml_layer),
            preserveViewport:true,
            suppressInfoWindows:true
         });
        
       ctaLayer.setMap(map);
     }
    }
  };
  Walkabout.loadJSON = function(data, map) {
     if(!data.map.layers) return;
      for(var i=0; i<data.map.layers.length;i++){
        if(data.map.layers[i] && data.map.layers[i].geojson_layer){
           map.data.loadGeoJson( encodeURI(wroot+'geojson_layers/'+ data.map.layers[i].geojson_layer));
        }
      }
  };


  /*
   * Set Time Interval
   */
  Walkabout.setTimeInterval = function(places) {
    var self = Walkabout,
    last=self.getLastDate(places),
    first=self.getFirstDate(places);

    self.interval.start.date =  moment(first).format();
 

    var now=moment();
   
    if(!first || (now.isBefore(last,'day') && now.isAfter(first,'day'))  ) {

      self.interval.current.date = now.format();
      self.interval.current.from = now.format();
      self.interval.current.to = now.format();
    }else{
      self.interval.current.date = moment(first).format();
      self.interval.current.from = moment(first).format();
      self.interval.current.to = moment(first).format();
    }
    self.interval.end.date = moment(last).format();
    self.refreshSlider();

  };

  Walkabout.previous = function() {
    var self = Walkabout;

    var current = self.interval.current.date;
    var start = self.interval.start.date;
    
   // if (current > start) {
      self.interval.current.date = moment(self.interval.current.date).subtract('1', 'day').format();
      self.interval.current.from = moment(self.interval.current.from ).subtract('1','day').format();
        self.interval.current.to = moment(self.interval.current.to ).subtract('1','day').format();

      self.refreshSlider();
      var hidden=!$calendar || $calendar.hasClass("calendar-hidden");
      $.extend(calendar.options,
        {
          selectedDate: new Date(self.interval.current.date)
        });
      calendar.render();

      if(hidden){
        $(".gldp-flatwhite").toggleClass('calendar-hidden');  
      }
      

    //}
  };

  Walkabout.formatDate=function(date){
    var daysOfWeel=[
    "Mon","Tue","Wed","Thu","Fri","Sat","Sun"
    ];
    return daysOfWeel[moment(date).format('E')-1] +' '+
               moment(date).format('MM/DD/YYYY');
  };
  Walkabout.refreshSlider=function(){
      var self = Walkabout;
      $('#text-date').text(
        self.formatDate(self.interval.current.date) +' '+
        /*moment(self.interval.current.date).format('MMMM D') + ', ' + */
        moment(self.interval.current.from).format('hh:mm a') + ' to ' + 
        moment(self.interval.current.to).format('hh:mm a')
      );
      self.filterOptions.dateInterval.from = self.interval.current.from;
      self.filterOptions.dateInterval.to = self.interval.current.to;
      
      self.refreshDisplay();
  };
  Walkabout.next = function() {
    var self = Walkabout;

    var current = new Date(self.interval.current.date);
    var end = new Date(self.interval.end.date);
    
        self.interval.current.date = moment(self.interval.current.date).add('1', 'day').format();
        self.interval.current.from = moment(self.interval.current.from ).add('1','day').format();
        self.interval.current.to = moment(self.interval.current.to ).add('1','day').format();
      
      var hidden=!$calendar || $calendar.hasClass("calendar-hidden");
      
        $.extend(calendar.options,
        {
          selectedDate: new Date(self.interval.current.date)
        });
        calendar.render();
        if(hidden){
          $(".gldp-flatwhite").toggleClass('calendar-hidden');  
        }
        

      self.refreshSlider();

    
  };

  Walkabout.filter = function(filters, dateInterval) {
    
    var self = Walkabout;

    //var dateIntervalFrom = new Date(moment(dateInterval.from).format());
    //var dateIntervalTo = new Date(moment(dateInterval.to).format());
    var date1=new Date(moment(dateInterval.from).format());
    //date1.setHours(0);
    //date1.setMinutes(0);
    //date1.setSeconds(0);
    var date2 = new Date(moment(dateInterval.to).format());
    //date2.setHours(23);
    //date2.setMinutes(59);
    //date2.setSeconds(59);

    //var time1=pad(dateIntervalFrom.getHours(),2)+""+pad(dateIntervalFrom.getMinutes(),2);
    //var time2=pad(dateIntervalTo.getHours(),2)+""+pad(dateIntervalTo.getMinutes(),2);
    var eventsCount=0;
    $.each(dataMap.markers,function(index,marker){
  
    if(marker.type === 2 ){
            var start = new Date(moment(date1).format());
            start.setHours(0);
            start.setMinutes(0);

            var end =new Date(moment(date2).format());
            end.setHours(23);
            end.setMinutes(59)
       
          if (marker.end >= start && marker.date <= end ) {
              eventsCount++;
          }  
      }
      if(filters.places[marker.cat] || filters.events[marker.cat]){


    
      
       
        if (marker.end >= date1 && marker.date <= date2 || marker.permanent) {

          if(!marker.getMap()){
            marker.setMap(dataMap.map);
          }
          /*var markerTime1=pad(marker.date.getHours(),2)+""+pad(marker.date.getMinutes(),2);
          var markerTime2=pad(marker.end.getHours(),2)+""+pad(marker.end.getMinutes(),2)
          if(parseInt(markerTime1,10)<=parseInt(time2,10) && parseInt(markerTime2,10)>=parseInt(time1,10)){
            if(!marker.getMap()){
              marker.setMap(dataMap.map);       
            }
          }else{
            marker.setMap(null);            
          }*/
        }else{
         
          marker.setMap(null);            
        }
      }else{
        marker.setMap(null);        
      }

    });
  
    if(eventsCount===0){
      $("#btn-events").hide();
      $("#timepicker-container-vertical").hide();
      $(".back-controls").hide();
      
    }else{
      $("#timepicker-container-vertical").show();
      $("#btn-events").show();
      $(".back-controls").show();
    }

  };

  Walkabout.getMinutes=function(time){
    if(time && time.split ){
      var t=time.split(":");
      return parseInt(t[0])*60+parseInt(t[1]);
    }
    return -1;
  };
  var currentValue=0;
  Walkabout.step=0;
  Walkabout.initializeTimeBar = function(data) {
  var self = Walkabout,
  min=self.getMinutes(data.start_time),
  max=self.getMinutes(data.final_time)-data.step,
  value= parseInt(((max+min)/2)/data.step)*data.step;
  self.step=data.step;

  var now= new Date(),
  minutes=now.getHours()*60+now.getMinutes();
  if(minutes >=min && minutes <=max){
    value=parseInt(minutes/data.step)*data.step;
  }

  $('#timeslider-vertical').slider({
  tooltip:'never',
  orientation: 'vertical',
  min: min,
  max: max,
  value: value,
  step: data.step,
  reversed: true
    }).on('slide',function(e){
      if(e.value!=currentValue){
        isDragging=true;
        currentValue=e.value;
        self.showDate(e.value);
      }
    }).on('slideStop',function(e){
      self.showDate(e.value);
      isDragging=false;
    });

  self.showDate(value);
  $(".slider-handle").html('<i class="icon-ic_time"></i> Time');
      

  };

  Walkabout.showUserDate = function() {
    var self = Walkabout;
    var userHour = moment().hour();
    var now = moment().format('YYYY-MM-DD');
    var current = moment(self.interval.current.date).format('YYYY-MM-DD');

    if (moment(now).isSame(current))
      return userHour;
    return false;
  };

  Walkabout.showDate = function(value) {
    var self = Walkabout;
      
    if (value >= 0 ) {
      var hours=parseInt(value/60),
        minutes=value%60;
      self.interval.current.from = moment(self.interval.current.from).set('hour', hours).set('minute',minutes);
      self.interval.current.to = moment(self.interval.current.from).set('hour',hours).set('minute',minutes).add(Walkabout.step-1,'minutes');
      $('#text-date').text(
        self.formatDate(self.interval.current.date) +" "+
        /*moment(self.interval.current.date).format('MMMM D') + ', ' + */
        moment(self.interval.current.from).format('hh:mm a') + ' to ' + 
        moment(self.interval.current.to).format('hh:mm a')
      );
    
      self.filterOptions.dateInterval.from = self.interval.current.from;
      self.filterOptions.dateInterval.to = self.interval.current.to;
    }


    self.refreshDisplay();
  };
  Walkabout.defaultOptions=function(){
      
    var self = Walkabout;
    var coords = self.entireMapOptions.center.split(',');
    dataMap.map.setOptions({
      'center': new google.maps.LatLng(parseFloat(coords[0], 10), parseFloat(coords[1], 10)),
      'zoom': parseInt(self.entireMapOptions.zoom, 10)
    });
  }
  var searching=false;
  Walkabout.entireMap = function(options) {

    options=options||{};
    var self = Walkabout;
 
    if(options.use_location){
        if (navigator.geolocation) {
          if(searching) return;
          searching=true;
          $("#loading_geo").show();
          navigator.geolocation.getCurrentPosition(function(position){
            searching=false;
            $("#loading_geo").hide();
            if(!position){ 

             self.defaultOptions();
              return;
            }
            var center=new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
            if(  dataMap.map.getBounds().contains( center )){
              dataMap.map.setCenter(center);
              dataMap.map.setZoom(dataMap.clientZoom);
              var markerURL = wroot + 'img/bluedot.png';
              self.clientMarker=self.clientMarker || new google.maps.Marker({
                position:  center,
                map: dataMap.map,
                icon: new google.maps.MarkerImage(markerURL),
                zIndex: 100
              });
            }else{
                  self.defaultOptions();
            }
           
          },function(err){
              searching=false;
              $("#loading_geo").hide();
           
               self.defaultOptions();
          });
        }else{
          self.defaultOptions();  
        }
        
        return;
    }else{
       self.defaultOptions();
    }
   
  };

  Walkabout.findMe = function() {
    var self = Walkabout;
    $('#map-canvas').gmap('getCurrentPosition', function(position, status) {
      if (status === 'OK') {
        var clientPosition = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
        $('#map-canvas').gmap('addShape', 'Circle', {
          center: $('#map-canvas').gmap('option', 'center'),
          strokeColor: '#fff',
          strokeWeight: 0.3,
          fillColor: "#fff",
          fillOpacity: 0.3,
          radius: 2000,
          clickable: false
        });
        var circle = $('#map-canvas').gmap('get', 'overlays > Circle')[0];
        var bounds = circle.getBounds();
        if (bounds.contains(clientPosition)) {
          var markerURL = wroot + 'img/bluedot.png';
          $('#map-canvas').gmap('addMarker', {
            'position': clientPosition,
            'icon': new google.maps.MarkerImage(markerURL),
            'bounds': true
          });
          $('#map-canvas').gmap('option', 'center', clientPosition);
        } else alert('You are too far :(');
        $('#map-canvas').gmap('clear', 'overlays > Circle');
      } else alert('Location unavailable.');
    }, {enableHighAccuracy: true, timeout: 10000});
    var userHour = moment().hour();
    $('#timebar input[type="range"]').val(userHour).change();
  };
})(window, jQuery);
