
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Walkabout Login</title>
    <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/styles.css')}}">
    <style>
      body,html{
        height: 100%;
      }
      /*Panel tabs*/
.panel-tabs {
    position: absolute;
    bottom: 20px;
    right: 2px;
    clear:both;
    border-bottom: 1px solid transparent;
}

.panel-tabs > li {
    float: left;
    margin-bottom: -1px;
}

.panel-tabs > li > a {
    margin-right: 2px;
    margin-top: 4px;
    line-height: .85;
    border: 1px solid transparent;
    border-radius: 4px 4px 0 0;
    
}

.panel-tabs > li > a:hover {
    border-color: transparent;
   
    background-color: transparent;
}

.panel-tabs > li.active > a,
.panel-tabs > li.active > a:hover,
.panel-tabs > li.active > a:focus {
    
    cursor: default;
    -webkit-border-radius: 2px;
    -moz-border-radius: 2px;
    border-radius: 2px;
    background-color: rgba(255,255,255, .23);
    border-bottom-color: transparent;
}
.panel-heading{
    height: 60px;
}
.panel-body{
    min-height: 250px;
    position: relative;
}
    </style>


</head>
<body>
    <div class="container" id="login">
    
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default" style="">
                <div class="panel-heading">
                    <img class="logo" src="{{asset('img/wlogo.png')}}">
                  

                </div>
                <div class="panel-body">
                   <div class="tab-content">
                       <div class="tab-pane active" id="tab1">
                       <form accept-charset="UTF-8" role="form" method="POST" action="{{url('login/logon')}}">
                      
                        <div class="form-group">
                            <input class="form-control" placeholder="E-mail" name="email" type="text">
                        </div>
                        <div class="form-group">
                            <input class="form-control" placeholder="Password" name="password" type="password" value="">
                        </div>
                        <input class="btn btn-lg btn-primary btn-block" type="submit" value="Login">
                        
                    
                        <div style="text-align:center; color:red">{{Session::get('message')}}{{Session::get('error')}}</div>
                        <div style="text-align:center;">{{Session::get('status')}}</div>
                    
                    </form>
                       </div>

                       <div class="tab-pane" id="tab2" style="padding-top: 30px;">
                            <form action="{{ action('RemindersController@postRemind') }}" method="POST">
                            <div class="form-group">
                                <input type="email"  placeholder="Email" class="form-control" name="email">
                                </div>
                                <div class="form-group">
                                <input type="submit" class="btn btn-primary btn-block" value="Send reminder">
                                </div>
                            </form>
                       </div>

                   </div>
                   
                        <ul class="nav panel-tabs">
                            <li class="active"><a href="#tab1" data-toggle="tab">Login</a></li>
                            <li><a href="#tab2" data-toggle="tab">Reset password</a></li>
                        </ul>
                   
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
</body>
</html>
