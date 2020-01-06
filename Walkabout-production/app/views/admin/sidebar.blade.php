
  <ul class="nav nav-sidebar">
 
    <li {{ (Request::is('admin/maps*') ? 'class="active"' : '') }}><a href="{{url('admin/maps')}}">Maps</a></li>
    <li {{ (Request::is('admin/layers*') ? 'class="active"' : '') }}><a href="{{url('admin/layers')}}">Layers</a></li>
    <li {{ (Request::is('admin/places*') ? 'class="active"' : '') }}><a href="{{url('admin/places')}}">Places</a></li>
    <li {{ (Request::is('admin/events*') ? 'class="active"' : '') }}><a href="{{url('admin/events')}}">Events</a></li>
      @if(Auth::user()->can("manage_categories"))
    <li {{ (Request::is('admin/categories*') ? 'class="active"' : '') }}><a href="{{url('admin/categories')}}">Categories</a></li>
    @endif
    @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Account Admin') )
    <li {{ (Request::is('admin/users*') ? 'class="active"' : '') }}><a href="{{url('admin/users')}}">Users</a></li>
    <li {{ (Request::is('admin/groups*') ? 'class="active"' : '') }}><a href="{{url('admin/groups')}}">Groups</a></li>
    
    @endif
    @if(Auth::user()->hasRole('Admin'))
    <li {{ (Request::is('admin/reports*') ? 'class="active"' : '') }}><a href="{{url('admin/reports')}}">Reports</a></li>
    @endif
  </ul>
