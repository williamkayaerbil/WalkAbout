
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Walkabout password reset</title>
    <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/styles.css')}}">
   

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

                  <form action="{{ action('RemindersController@postReset') }}" method="POST">                  
                    <input type="hidden" name="token" value="{{ $token }}">  
                    <div class="form-group">
                        <input  placeholder="Email" type="email" name="email" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <input placeholder="Password" type="password" class="form-control" name="password">    
                    </div>
                    
                    <div class="form-group">
                        <input placeholder="Password confirmation" type="password" class="form-control" name="password_confirmation">
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary btn-block" value="Reset Password">
                    </div>
                    
                    
                </form>
                <div style="text-align:center; color:red">{{Session::get('message')}}{{Session::get('error')}}</div>
                       </div>

                   

                   </div>
                   
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
</body>
</html>
