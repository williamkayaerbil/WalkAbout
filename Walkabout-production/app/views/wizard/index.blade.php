  
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Walkabout Login</title>
    <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/wizard.css')}}">
    <link rel="stylesheet" href="{{asset('js/lib/jquerydd/dd.css')}}">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">

    <style>
      body,html{
        height: 100%;
      }

    </style>


</head>
<body>
    <div class="container" id="login">
    
    <div class="row">
    <div class="col-md-2 col-sm-2 col-xs-3 side-menu">
      <div class="panel panel-default side-panel" >
          <ul class="nav">
            <li class="active"><a href="#tab1" data-toggle="tab">Account</a></li>
            <li><a href="#tab2" data-toggle="tab">Map</a></li>
            <li><a href="#tab3" data-toggle="tab">Places</a></li>
            <li><a href="#tab4" data-toggle="tab">Events</a></li>
            <li><a href="#tab5" data-toggle="tab">Categories</a></li>
        </ul>
        </div>
    </div>
        <div class="col-md-10 col-sm-10 col-xs-9" style="border:0">
            <div class="panel panel-default" style="">
                <div class="panel-heading">
                      <ul class="nav panel-tabs">
                            <li class="active"><a href="#tab1" data-toggle="tab">Account</a></li>
                            <li><a href="#tab2" data-toggle="tab">Map</a></li>
                            <li><a href="#tab3" data-toggle="tab">Places</a></li>
                            <li><a href="#tab4" data-toggle="tab">Events</a></li>
                            <li><a href="#tab5" data-toggle="tab">Categories</a></li>
                        </ul>
                </div>
                <div class="">
                   <div class="tab-content">

                       <div class="tab-pane active col-md-6 col-sm-6 col-lg-6" id="tab1">
                       
                       <h2>Everything look correct?</h2>
                       <ul>
                           <li>Name: joe</li>
                           <li>Email: joe@gmail.com</li>
                           <li>Username: joe</li>
                           <li>Password: *****</li>
                           <li>Security Question: where I did grow up?</li>
                           <li>Answer: Grand Haven </li>
                           <li>Plan: Starter</li>
                        </ul>
                    </div>

                       <div class="tab-pane col-md-12 col-sm-12 col-lg-12" id="tab2">
                            <div class="col-md-6 col-sm-6 col-lg-6">
                                
                            
                           <h2>Let's Set Up Your Map</h2>
                           <div class="col-md-12">
                               <div class="form-group">
                                    <label>Name</label>
                                    <input class="form-control"/>
                                </div>
                                <div class="form-group">    
                                    <label>Description</label>
                                    <input class="form-control"/>
                                </div>
                                <div class="form-group">
                                    <label>Center Point (tell us the latitude and longitude, or simply drag and drop the pin to the right)</label>
                                    <input class="form-control"/>
                                 </div>

                                 <div class="form-group">
                                    <label>Header Logo</label>
                                    <input class="form-control" type="file"/>
                                    
                                 </div>

                                <div class="form-group">
                                    <label>Menu (Choose up to 5)</label>
                                    <input class="form-control"/>
                                 </div>
                             </div>
                             </div>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                            <input type="text" name="location"  id="pac-input" class="controls", placeholder="Search Address" />
                                <div id="map"></div>
                            </div>
                       </div>

                      <div class="tab-pane col-md-12 col-sm-12 col-lg-12" id="tab3">
                      <div class="col-sm-12 col-md-12 col-lg-12">
                           <h2>Let's Put Some Places on Your Map (add up to 5 now)</h2>
                            <p>Upload (download our custom template now):
                            <br>
                            or
                            <br>
                            Manually Add Places</p>
                            <p>Right click to add a new place and left click over marker to edit information</p>
                       </div>
                       <div class="col-sm-12 col-md-12 col-lg-12">
                            <div id="map-places"></div>
                       </div>
                       </div>
                      <div class="tab-pane col-md-12 col-sm-12 col-lg-12" id="tab4">
                      <div class="col-sm-12 col-md-12 col-lg-12">
                           <h2>Now Let's Add Some Events (add up to 5 now)</h2>
                            <p>Upload (download our custom template now):
                            <br>
                            or
                            <br>
                            Manually Add Events</p>
                            <p>Click on a place to add new event</p>
                       </div>
                       <div class="col-sm-12 col-md-12 col-lg-12">
                            <div id="map-events"></div>
                       </div>
                       </div>

    
    
    

                    <div class="col-md-6 col-sm-6 col-lg-6 panel-map">
                    
                    
                    
                    <div id="map-events" style="display:none"></div>
                    

                    <div class="buttons">
                
                            <button class="btn btn-default">I'll Do This Later</button>
                            <button class="btn btn-default">Go Back</button>
                            <button class="btn btn-primary">Next</button>
                    </div>
                        
                    </div>
                   </div>
                   
                    
                   
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="categoryModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Add new Category</h4>
      </div>
      <div class="modal-body">
        <form>
      <div class="form-group">
        <label for="name">Title</label>
        <input type="text" class="form-control" id="title" placeholder="Title">
      </div>
      <div class="form-group">
        <label for="color">Color</label>
        <input type="color" class="form-control" id="color" placeholder="#000000">
      </div>
      <div class="form-group">
        <label for="icon">Icon</label>
        <div id="preview-image">
          <img id="image"  height="50" src="{{asset('img/no-img.jpg')}}">
        </div>
        <input type="file" id="icon">
        <p class="help-block">Upload an icon or select one</p>
        <ul id="icons" >
        @foreach (Icon::all() as $key => $icon) 
          <li><img class="icon-cat" data-icon="{{$icon->name}}" src="{{asset('img/icons/'.$icon->name)}}"></li>
        @endforeach
        </ul>
        <br>
        <br>
        <br>
      </div>
    </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary save-category" >Save</button>

      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<canvas id="canvas"></canvas>
<canvas id="canvas2"></canvas>
<script src="//maps.googleapis.com/maps/api/js?key=AIzaSyBtik1WlRukGIeooM_iQirsnrj59gDFHV8&v=3&libraries=geometry,places"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
<script type="text/javascript" src="{{asset('js/lib/infobox.js')}}"></script>
<script type="text/javascript" src="{{asset('js/lib/jquerydd/jquery.dd.min.js')}}"></script>
<script type="text/javascript">

'use strict';
(function($) {


    $.eventReport = function(selector, root) {
        var s = [];
        $(selector || '*', root).addBack().each(function() {
            // the following line is the only change
            var e = $._data(this, 'events');
            if(!e) return;
            s.push(this.tagName);
            if(this.id) s.push('#', this.id);
            if(this.className) s.push('.', this.className.replace(/ +/g, '.'));
            for(var p in e) {
                var r = e[p],
                    h = r.length - r.delegateCount;
                if(h)
                    s.push('\n', h, ' ', p, ' handler', h > 1 ? 's' : '');
                if(r.delegateCount) {
                    for(var q = 0; q < r.length; q++)
                        if(r[q].selector) s.push('\n', p, ' for ', r[q].selector);
                }
            }
            s.push('\n\n');
        });
        return s.join('');
    }
    $.fn.eventReport = function(selector) {
        return $.eventReport(selector, this);
    }
    var canvas=document.getElementById('canvas');
    var canvas2=document.getElementById('canvas2');
    var selectedImage=$("#image").get(0);

    $("#icon").on('change',function(){
       var reader = new FileReader();
          reader.onload = function (e) {
            var img=new Image();
            img.src=e.target.result;
            selectedImage=img;
            img.onload=function(){
                $("#image").attr('src',createMarker(300,300,img,$("#color").val()));
            };
          }
          reader.readAsDataURL(this.files[0]);
    });
    $(".icon-cat").on('click',function(){
      $("#input-icon").val($(this).data("icon"));

      selectedImage=$(this).get(0);
      $("#image").attr("src",createMarker(300,300,$(this)[0],$("#color").val()));


    });
    $("#color").on('change',function(){
        $("#image").attr("src",createMarker(300,300,selectedImage,$(this).val()));       
    });



function createMarker(width, height,thumbImg,color) {
      canvas.globalAlpha = 1;
      canvas.width = width;
      canvas.height = height;
      var context = canvas.getContext("2d");
      var pixelRatio = window.devicePixelRatio || 1;
      //pixelRatio*=2;
      width=width/pixelRatio;  
      height=height/pixelRatio;
      context.setTransform(pixelRatio,0,0,pixelRatio,0,0);
      context.clearRect(0,0,width,height);
    
      context.fillStyle = "#fff";  
       context.beginPath();
        context.arc(width/2+1, height/2+1, width/2-4, 0, Math.PI * 2);
        context.closePath();
            
      context.fill();
        context.lineWidth = 3;
        context.clip();   
        context.globalCompositeOperation = "destination-in"; 
      if(thumbImg){
          var ctx = canvas2.getContext("2d");
           try{
          ctx.drawImage(thumbImg,0, 0,50,50); //Fail with SVG    
            //context.putImageData(map,0,0, parseInt(width*0.5-width*0.3,10)+1, parseInt(height*0.5-height*0.3,10)+1,parseInt(width*0.6,10),parseInt(height*0.6,10));
            context.drawImage(thumbImg, parseInt(width*0.5-width*0.3,10)+1, parseInt(height*0.5-height*0.3,10)+1,parseInt(width*0.6,10),parseInt(height*0.6,10)); //Fail with SVG
          }catch(e){
            console.log(e);
          }
        }

        context.globalCompositeOperation = "destination-over";  
        context.fillStyle = color;  
      

      context.beginPath();
        context.arc(width/2+1, height/2+1, width/2-4, 0, Math.PI * 2);
        context.closePath();
            
      context.fill();
        context.lineWidth = 3;
        context.clip();  
        //
      return canvas.toDataURL();
 
}



var lib={
    getMap: function(idMap){
       var map=new google.maps.Map(document.getElementById(idMap));
        map.setOptions({
              mapTypeControl: false,
              panControl: false,
              zoomControl: false,
              streetViewControl: false
              
        });
        return map;
    },
    maps: {
      map:null,
      marker:null,
      idMap:"map",
      initialize: function(latitude,longitude){
          this.map=lib.getMap(this.idMap);
          this.map.setCenter(new google.maps.LatLng(latitude,longitude));
          this.map.setZoom(12);

          var input = document.getElementById('pac-input');
          this.map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
          this.searchBox = new google.maps.places.SearchBox(input);
          google.maps.event.addListener(this.searchBox, 'places_changed', this.places_changed.bind(this));        
          this.marker = new google.maps.Marker({
              map: this.map,
              position: new google.maps.LatLng(0,0),
              draggable:true
          });
          google.maps.event.addListener(this.map, 'bounds_changed', function() {
              var bounds = this.map.getBounds();
              this.searchBox.setBounds(bounds);
          }.bind(this));
          
      },
   
      places_changed: function(){
              var places = this.searchBox.getPlaces(),
              place;
              
              if (places.length == 0) {
                return;
              }     
              place=places[0];
              var bounds = new google.maps.LatLngBounds();
              this.marker.setPosition(place.geometry.location);
              bounds.extend(place.geometry.location);
              this.map.fitBounds(bounds);
      }
    },
    places:{
       map:null,
       idMap: "map-places",
       markers:[],
       categories:[],
       initialize: function(){
          this.map=lib.getMap(this.idMap);
          this.refresh();
          this.map.setZoom(12);
          google.maps.event.addListener(this.map,'rightclick',this.addMarker.bind(this));

        
       },
       refresh: function(){
          this.map.setCenter(lib.maps.map.getCenter());
       },
       getContent: function(marker,infobox){
        var that=this,content;
        if(!marker.info || !marker.info.name || marker.info.edit){
            marker.info=marker.info||{
              name:'',
              description:'',
              img:false,
              location:'',
              category_id:-1
            };
 
           if(marker.info.edit) {marker.info.edit=false;}
            var lng=marker.position.B || marker.position.D;
            content= $('<div class="infobox" style="overflow:visible"><form style="overflow: visible">'+
                  '<h2><input  value="'+marker.info.name+'" type="text" name="name" class="place-name form-control" placeholder="Name"/></h2>'+ 
                    '<p><img height="50" width="50"  src="'+(marker.info.img?marker.info.img:"{{asset("img/no-img.jpg")}}")+'" style="float:left">'+
                     '<textarea placeholder="Description" class="place-description form-control">'+marker.info.description+'</textarea></p>'+
                     '<div class="input-group input-group-sm"><span class="input-group-addon">Category</span><select placeholder="Category" class="place-category form-control"></select> <span class="input-group-btn"><button class="btn btn-primary add-category" data-toggle="modal" data-target="#categoryModal"  type="button"><i class="fa fa-plus"></i></button></div>'+
                     //'<select class="place-category"></select>'+
                     '<div class="input-group input-group-sm"><span class="input-group-addon">Position</span><input type="text" class="place-location form-control" placeholder="Position (Lat,Lng)" value="'+marker.position.k+','+lng+'"/></div>'+
                     '<p class="text-right"><a href="#" class="save-place">Save</a> | <a class="delete-place" href="#">Delete</a></p>'+
                     '<input type="file" class="place-photo" style="display:none">'+
                  '</form></div>');
            infobox.setContent(content[0]);
            var saveButton=content.find('.save-place')[0],
                photo=content.find('img')[0],
                categorySelect=content.find('.place-category')[0],
                file=content.find('.place-photo')[0],
                addCategoryButton=content.find('.add-category')[0];
            var html=[];
            for(var i=0; i<that.categories.length;i++){
              var cat=that.categories[i];
              if(marker.info.category_id==i){
                html.push('<option selected="selected" value="'+i+'" data-image="'+cat.image+'">'+cat.name+'</option>');
              }else{
                html.push('<option value="'+i+'" data-image="'+cat.image+'">'+cat.name+'</option>');  
              }
              
            }
            $(categorySelect).html(html.join(""));
     
          
             file.onchange=function(){
              var reader = new FileReader();
                reader.onload = function (e) {
                    photo.src=marker.info.photo=e.target.result;
                }
                reader.readAsDataURL(this.files[0]);
             };
             google.maps.event.addDomListener(photo,'click',function(event){
                file.click();
             });
              google.maps.event.addDomListener(addCategoryButton,'click',function(event){
                var modal=$("#categoryModal");
                modal.modal();
                 var saveCategory=modal.find('.save-category');
                
                 modal.modal();
                 $(saveCategory).off("click");
                 $(saveCategory).on('click',function(){
                      var title=modal.find("#title"),image;
                      image=$("#image");
                      if(title.val()===""){
                        alert('Please write a title')
                        return;
                      }
                      if(image.attr("src").indexOf("no-img")!==-1){
                        alert("Please select an icon");
                        return;
                      }
                      that.categories.push({
                        name: title.val(),
                        image: $("#image").attr("src"),
                        icon: new google.maps.MarkerImage(  $("#image").attr("src"),null,null,null,new google.maps.Size(32,32) )
                      });
                      html=[];
                      for(var i=0; i<that.categories.length;i++){
                        var cat=that.categories[i];
                        html.push('<option value="'+i+'" data-image="'+cat.image+'">'+cat.name+'</option>');
                      }
                      if(html.length>0){
                        var dropdown=null;
                         dropdown=$(categorySelect).msDropdown().data('dd'); 
                         dropdown.destroy();
                         $(categorySelect).html(html.join(""));
                         dropdown=$(categorySelect).msDropdown().data('dd'); 
                      }
                      modal.modal('hide');
                 });
                 
             });
            $(saveButton).on('click',function(e){
              e.preventDefault();
            });
            google.maps.event.addDomListener(saveButton,'click',function(event){
                var name=content.find('.place-name').val(),
                    description=content.find('.place-description').val(),
                    location=content.find('.place-location').val(),
                    img=content.find('.place-img').val(),
                    position=location.split(",");
                var category=$(categorySelect).val();
                if(category==null){
                  alert("Select a category");
                  return;
                }
                if(name=="" ){
                  alert("Please enter name");
                  return;
                }
                if(!parseInt(position[0],10) || !parseInt(position[1],10)){
                  alert("Invalid latitude/longitud");
                  return;
                }
                marker.info={
                  description:description,
                  location:location,
                  img:photo.src,
                  name:name,
                  category_id:category,
                  category: that.categories[category]

                };      
                marker.setIcon(that.categories[category].icon);
                infobox.setContent(that.getContent(marker,infobox)[0]);
                //content.replaceWith(that.getContent(marker));
                marker.setPosition(new google.maps.LatLng(position[0],position[1]));
                that.map.setCenter(marker.getPosition());

            });

            var deleteButtton=content.find('.delete-place')[0];
            $(deleteButtton).on('click',function(e){
              e.preventDefault();
            });
            google.maps.event.addDomListener(deleteButtton,'click',function(e){
               infobox.close();
               marker.setMap(null);
            });
            return content;
        }
        content=$('<div class="infobox">'+
                  '<h2>'+marker.info.name+'</h2>'+ 
                    '<p><img height="50" width="50"  src="'+(marker.info.img?marker.info.img:"{{asset("img/no-img.jpg")}}")+'" style="float:left">'+
                    marker.info.description+'</p>'+
                     '<p class="text-right"><a class="edit-place" href="#">Edit</a> | <a class="delete-place" href="#">Delete</a></p>'+
                  '</div>');
            infobox.setContent(content[0]);
            var deleteButtton=content.find('.delete-place')[0],
                editButton=content.find('.edit-place')[0];
            $(deleteButtton).on('click',function(e){
              e.preventDefault();
            });
            $(editButton).on('click',function(e){
              e.preventDefault();
            });
            google.maps.event.addDomListener(editButton,'click',function(){
                marker.info.edit=true;
                var content=that.getContent(marker,infobox)[0];
                infobox.setContent(content);
                var cat=$(content).find('.place-category'); 
                $(cat).msDropdown().data('dd');
            });
            google.maps.event.addDomListener(deleteButtton,'click',function(){
               infobox.close();
               marker.setMap(null);
            });
            return content;

       },
       addMarker:function(e){
        var map=this.map,
        that=this,
         marker = new google.maps.Marker({
              position: e.latLng,
              map: map,
              draggable:true,
              animation: google.maps.Animation.DROP
          }),
         
         index=this.markers.length-1,
         infobox = new InfoBox({
         //content:content[0],
         disableAutoPan: false,
         boxStyle: {
            overflow: "visible"
         },
         //enableEventPropagation:true,
         maxWidth: 150,
         pixelOffset: new google.maps.Size(-150, -150),
         zIndex: null,
        closeBoxMargin: "12px 4px 2px 2px",
        closeBoxURL: "http://www.google.com/intl/en_us/mapfiles/close.gif",
        infoBoxClearance: new google.maps.Size(1, 1),
    });
        this.getContent(marker,infobox),
        google.maps.event.addListener(infobox, "closeclick",function(){
            if(marker.info){
              marker.info.edit=false;
            }
            infobox.setContent(that.getContent(marker,infobox)[0]);
        });

         google.maps.event.addListener(infobox, "domready",function(){
            var select=$(infobox.getContent()).find('.place-category');
            $(select).msDropdown().data('dd');
        });
        google.maps.event.addListener(marker, 'click', function() {
            infobox.open(map, this);
            map.panTo(e.latLng);
        });
        google.maps.event.addListener(map, 'click', function() {
            if(marker.info){
              marker.info.edit=false;
            }
            infobox.setContent(that.getContent(marker,infobox)[0]);
            infobox.close();
        });
        this.markers.push(marker);
       }
    }
};
lib.maps.initialize("33.639721","-84.442498");
lib.places.initialize();






$('.panel-tabs a').click(function (e) {
    e.preventDefault();
    setTimeout(function(){
        google.maps.event.trigger(lib.maps.map, 'resize');
        google.maps.event.trigger(lib.places.map, 'resize');
    },100);

});

})(jQuery);


</script>
</body>
</html>
