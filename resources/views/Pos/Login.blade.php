<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=10" >
    <meta name="csrf-token" content="{{csrf_token() }}" >
    <meta name="viewport" content="width=device-width, initial-scale=1" />
        {!! HTML::style('assets/css/vendor/bootstrap.min.css') !!}
        {!! HTML::style('assets/css/vendor/font-awesome.min.css') !!}

    
    <!-- SCRIPTS -->
        {!! HTML::script('assets/js/vendor/jquery/jquery-1.11.2.min.js') !!}
        {!! HTML::script('assets/js/vendor/bootstrap/bootstrap.min.js') !!}

    
    <title>ORG POS - Login</title>
</head>
<body class="noselect">


<style>

body {
    background: url("../../assets/images/pos_login_bg.jpg");
    background-size: cover;
}
    .login_box {
        display: block;
        margin:120px auto;
        max-width: 340px;
        box-shadow: 0px 0px 18px #3E3E3E;
        background: #fff;
        position: relative;
    }

    .login_box form {
        padding: 20px 28px;
    }

.login_box h2 {
    font-size: 18px;
margin-bottom: 5px;
text-align: center;
background: #27A9AE ;
color: #fff;
padding: 16px;
border-bottom:1px solid #727272;
font-family: Oswald;
}

.login_box button {
    margin:auto;
    display: table;
    margin-top: 15px;
    border-radius: 0;
    text-transform: uppercase;
    font-size: 12px;
    font-weight: bold;
    padding: 6px 20px
}
p.error_msg {
    position: absolute;
top: -43px;
background: #C24646 none repeat scroll 0% 0%;
margin: auto;
color: rgb(255, 255, 255);
left: 0px;
right: 0px
}

p.error_msg:not(:empty) {
  padding: 6px
}
</style>



<div class="login_box">

@if(isset($errors))
   {!! "<p class='text-danger text-center error_msg'>".$errors->first()."</p>" !!}
@endif

    <h2>ORG POS - User Authentication</h2>
    <form name="reg_form" style="max-width:500px;" id="waiter_reg_form" action="{{ action('AuthController@store')}}" method="post">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

  <div class="form-group">
    <label for="username">Username</label>
    <input type="text" name="username" id="username" class="form-control" placeholder="Username">
  </div>

  <div class="form-group">
    <label for="password">Password</label>
    <input type="password" name="password" class="form-control" id="password" autocomplete="off">
  </div>



  <button type="submit" class="btn btn-default">Login <i class="fa fa-unlock-alt"></i></button>
</form>
</div>




<div class="grid footer">
    <p class="text-center" style="margin-bottom:0px">
      ORG Point of Sale part of ORG Software Suite 
    </p>
    <p class="text-center">&copy; 2015 KLAXYCOM </p>
</div>


</body>
</html>
