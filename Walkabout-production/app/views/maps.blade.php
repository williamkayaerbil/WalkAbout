<!DOCTYPE html><html>
    <head>
        <title>Walkabout</title><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no">
        <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/normalize/3.0.1/normalize.min.css" />
        <link rel="stylesheet" href="{{asset('css/bootstrap-slider.css')}}">
        <link rel="stylesheet" href="{{asset('css/idangerous.swiper.css')}}">
        <!--<link rel="stylesheet" type="text/css" href="{{asset('css/fonts.css')}}" />
        <link rel="stylesheet" type="text/css" href="{{asset('css/ionicons.min.css')}}" />-->
        <!--<link rel="stylesheet" type="text/css" href="{{asset('css/design.css')}}" />-->
        <!--<link rel="stylesheet" type="text/css" href="{{asset('css/responsive.css')}}" />-->
        <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">

        <link rel="stylesheet" type="text/css" href="{{asset('/js/lib/glDatePicker/styles/glDatePicker.flatwhite.css')}}">

        <base target="_blank">
        <script>
                (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

                ga('create', 'UA-53675318-2', 'auto');
                ga('send', 'pageview');
        </script>
        <link rel="stylesheet" type="text/css" href="{{asset('css/styles.min.css')}}" />
        
        <script type="text/javascript">
        if(/iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream){
          document.querySelector('meta[name=viewport]')
            .setAttribute(
              'content', 
              'initial-scale=1.0001, minimum-scale=1.0001, maximum-scale=1.0001, user-scalable=no'
            );
        }
    </script>
    </head>
    <body>
        <div id="new-window" style="display:none">
            <button id="new-window-close" class=""><i class="fa fa-close"></i></button>
            <iframe id="new-window-iframe"></iframe>
        </div>
        <div class="navbar base">
            <a id="btn-burger" class="btn nav-left" href="#" >
                <i class="icon-ic_burger"></i>
            </a>

            <a href="#" id="logo-header-url"><img id="logo-header" src="{{asset('img/icons/logo.svg')}}" class="logo"></a>
            <a id="entire-map-button" href="#" class="btn nav-right">
                <i class="icon-ic_expandmap "></i>
            </a>

        </div>



        <div style="display:none" id="loading_geo" style="z-index:1000;background:#fff; position:absolute; width:100%;text-align:center;">
            Searching location...
        </div>
        <div class="side-menu left" id="side-burger">
            <a href="#" id="logo-burger-url"><img  id="logo-burger" src="{{asset('img/icons/logo.svg')}}" class="logo-left" alt="Walkabout"></a>
            <ul  class="menu-left" id="menu-burger">


            </ul>
        </div>
        <div id="loading">
            <span id="text-loading">Loading...</span>
        </div>
        <div id="content">

            <div id="map-canvas" style="width: 100%; height:100%;"></div>

            <a href="#"  id="btn-places" class="btn btn-round">
                <i class="icon-ic_places"></i>
                Places
            </a>
            <a href="#"  id="btn-events" class="btn btn-round">
                <i class="icon-ic_events"></i>
                Events
            </a>



            <div class="back-controls"></div>
            <div id="timepicker-container-vertical" >
                <input type="text"  style="width:100%" id="timeslider-vertical">
            </div>



            <div id="bottom-bar">
                <a href="#" id="previous"><i class="icon-ic_previous bottom left"></i></a>
                <span id="content-date"><i class="icon-ic_calendar"></i> <span id="text-date">September 8, 04:00 pm to 4:59 pm</span></span>
                <a href="#" id="next"><i class="icon-ic_next bottom right"></i></a>
            </div>


        </div>
        <div class="side-menu right" id="side-events">
            <!--<div class="search-content">
                <i class="icon-ic_search"></i>
                    <input type="text" class="search" placeholder="Search an event">
            </div>-->

            <div class="side-content"  >
                <div class="logo-container" style="text-align:center">
                    <a id="menu-logo-events" href="#"></a>
                </div>
                <h2>Events</h2>
                <p>Filter your interests</p>
                <div class="input-search">
                    <i class="fa fa-search"></i>
                    <input type="text" placeholder="Search" class="" id="search-events">
                </div>
                <ul class="events">
                    <li class="ico catEvent allEvents">
                        <input type="checkbox" id="allEvents" name="categories" value="tevent" checked>
                        <label for="allEvents">
                            <span> All Events </span>
                            <img src="{{asset('img/icons/ic_events.png')}}" alt="All Events">
                        </label>
                    </li>
                </ul>
            </div>
        </div>
        <div class="side-menu right" id="side-places">
            <!--<div class="search-content">
                <i class="icon-ic_search"></i>
                    <input type="text" class="search" placeholder="Search a place">
            </div>-->
            <div class="side-content">
                <div class="logo-container" style="text-align:center">
                    <a id="menu-logo-places" href="#"></a>
                </div>
                <h2>Places</h2>
                <p>Filter your interests</p>
                <div class="input-search">
                    <i class="fa fa-search"></i>
                    <input type="text" placeholder="Search" class="" id="search-places">
                </div>
                <ul class="places">
                    <li class="ico catPlace allPlaces">
                        <input type="checkbox" id="allPlaces" name="categories" value="tplace" checked>
                        <label for="allPlaces">
                            <span> All Places </span>
                            <img src="{{asset('img/icons/ic_places.png')}}" alt="All Places">
                        </label>
                    </li>
                </ul>
            </div>
        </div>
        <div id="calendar"  style="">
            <input type="text" name="glcalendar" id="glcalendar" style="width:100%; visibility:hidden">
        </div>
        <div id="first-visitor">

            <div class="swiper-container" id="swiper-first-visitor">
                <div class="swiper-wrapper">
                    <!--First Slide-->
                    <div class="swiper-slide" id="welcome"> 
                        <div class="first-page-title">Welcome to Walkabout</div>
                        <div class="subtitle">Enjoy the experience</div>
                        <div class="content">
                            <i class="icon-ic_places"></i>
                            <div class="subtitle">Share your location</div>
                            <p>This map will ask for permission to <br> access your current location. <br> If you agree, your position <br> will be shown continuously on the map.</p>
                        </div>
                        <div class="footer">
                            <a href="#" id="read-terms">Read terms and conditions</a>	
                        </div>
                    </div>

                    <!--Second Slide-->
                    <div class="swiper-slide" id="second">
                        <h3>Place and Events Filters</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                            consequat. 
                        </p>
                        <h3>Time Slider shows Events and Times</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                            tempor incididunt ut labore et dolore magna aliqua.</p>
                    </div>

                    <!--Third Slide-->
                    <div class="swiper-slide " id="third"> 
                        <div class="content">
                            <h3>Menu of Links</h3>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                                tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                                quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                                consequat. 
                            </p>
                            <h3>Calendar on Footer</h3>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                                tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                                quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                                consequat. 
                            </p>
                        </div>
                        <div class="buttons">
                            <button class="btn btn-accept" id="btn-accept">Accept</button>
                            <button class="btn btn-cancel" id="btn-cancel">Cancel</button>
                        </div>
                    </div>


                </div>

            </div>
            <div class="pagination"></div>
        </div>
        <div id="help">
            <div class="swiper-container" id="swiper-help">
                <div class="swiper-wrapper">
                    <div class="swiper-slide" > 
                        <i class="icon-ic_events"></i>
                        <div class="first-page-title">Events Menu</div>
                        <div class="content">Select your favorite interests <br> to show up on the map</div>
                    </div>
                    <div class="swiper-slide" > 
                        <i class="icon-ic_places"></i>
                        <div class="first-page-title">Places Menu</div>
                        <div class="content">Select your favorite places <br> to show up on the map</div>
                    </div>

                    <div class="swiper-slide" > 
                        <i class="icon-ic_places"></i>
                        <div class="first-page-title">Time Bar</div>
                        <div class="content">Slide up or down to choose <br> an hour of the day</div>
                    </div>


                    <div class="swiper-slide" > 
                        <i class="icon-ic_places"></i>
                        <div class="first-page-title">Timeline</div>
                        <div class="content">Browse your desire date <br> from the timeline bar at bottom</div>
                    </div>
                    <div class="swiper-slide" > 
                        <i class="icon-ic_places"></i>
                        <div class="first-page-title">Everything clear?</div>
                        <div class="content">Swap left to read it again</div>
                        <button class="btn btn-yes">Yes!</button>
                    </div>
                </div>
            </div>
            <div id="pagination-help"></div>
        </div>
        <div  id="overlay" class="overlay"></div>
        <div  id="overlay-calendar" class="overlay"></div>
        <div  id="overlay-first-visitor" class="overlay"></div>
        <div id="overlayInfo" class="overlay overlayInfo"></div>
        <canvas id="canvas" style="display:none"></canvas>
        <canvas id="canvas2" style="display:none"></canvas>

        <script type="text/javascript">
            var wroot = "{{url('/')}}/";

            var params = {"hash": "{{$hash}}", "userid": 1, "groupid": 1};
        </script>

        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBtik1WlRukGIeooM_iQirsnrj59gDFHV8&amp;v=3&amp;libraries=geometry"></script>
        <script type="text/javascript" src="{{asset('js/lib/jquery-2.1.1.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('js/lib/infobox.js')}}"></script>
        <script src="{{asset('js/bootstrap-slider.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('js/lib/moment.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('js/lib/idangerous.swiper.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('js/map.js')}}"></script>
        <script type="text/javascript" src="{{asset('/js/lib/glDatePicker/glDatePicker.min.js')}}"></script>
        <script>



    $(document).on('ready', function () {

        /*("touchmove", function(event){
         event.preventDefault();
         event.stopPropagation();
         });*/
     
        $('.navbar,.back-controls,#btn-events,#btn-places,#bottom-bar').on('touchmove', function (event) {
            event.preventDefault();
            event.stopPropagation();
        });

   
        $("#btn-events").on('click', function (e) {
            e.preventDefault();
            $('#side-events').toggleClass('right-active active');
            $("#overlay").show();
        });


        $("#btn-places").on('click', function (e) {
            e.preventDefault();
            $('#side-places').toggleClass('right-active active');
            $("#overlay").show();
        });

        $("#overlay").on('click', function () {
            $('.side-menu.active').toggleClass('right-active active');
            $('#side-burger.left-active').toggleClass('left-active active');
            $("#overlay").hide();
        });
        $("#btn-burger").on('click', function (e) {
            e.preventDefault();
            $('#side-burger').toggleClass('left-active active');
            $("#overlay").show();
        });



    });
        </script>

    </body>
</html>