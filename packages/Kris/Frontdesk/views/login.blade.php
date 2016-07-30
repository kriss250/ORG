<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Frondesk Login</title>
    {!!HTML::style("assets/css/vendor/bootstrap.min.css")!!}
    {!!HTML::style("assets/css/fo-login.css")!!}
</head>

<body>
    <div class="login-form">
        <h3 class="text-center">Frontdesk Login</h3>
       <div class="login-icon">
           <img src="/assets/images/Contact-Lock.png" />
       </div>
        @if(\Session::has("errors"))
        <div class="alert alert-danger">
            {{\Session::get("errors")->first()}}
        </div>
        @endif

        {!! Form::open(["method"=>"post","form"=>"class","action"=>"\Kris\Frontdesk\Controllers\UsersController@login" ]) !!}
        <label>Username</label>
        {!! Form::input("text","username",old("username"),["class"=>"form-control"])!!}
        <label>Password</label>
        {!! Form::input("password","password","",["class"=>"form-control"])!!}
        <br />
        {!! Form::input("submit","","Login",["class"=>"btn btn-success"])!!}

        {!!Form::close()!!}
    </div>
</body>
</html>



