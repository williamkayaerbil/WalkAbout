 <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="http://www.walkaboutapp.com"><img style="height:33px" src="{{asset('img/wlogo.png')}}"></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
        <ul class="nav navbar-nav navbar-left">
           
        </ul>
          <ul class="nav navbar-nav navbar-right">

<li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{Auth::user()->name}} <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="{{url('admin/settings')}}">Settings</a></li>
          </ul>
        </li>

            
            <li><a href="{{url('admin/info')}}"> Available Maps <span class="badge">{{Auth::user()->getAvailableMaps()}}</span></a></li>
            <li><a href="{{url('admin')}}">Home</a></li>
            <li><a href="{{url('/login/logout')}}">Logout</a></li>
          </ul>
        </div>
      </div>
    </nav>