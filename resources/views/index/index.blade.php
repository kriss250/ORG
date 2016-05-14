<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>ORG Software suite</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="author" content="Hakizimana Christian">
	<meta name="viewport" content="width=device-width, initial-scale=1">
        {!! HTML::style('assets/css/vendor/font-awesome.min.css') !!}

</head>
<style type="text/css">
body {
	background: url('/assets/images/main_bg.jpg');
}

.thebox {
	left: 0;
	right: 0;
	color: #fff;
	position: relative;
	margin-top: 15%;
	text-align: center;
	font-family: lato;
	font-weight: 300
}

.thebox h1 {
	font-family: lato;
	font-size: 36px;
	font-weight: 700;
	margin-bottom: -12px;
}

.thebox a {
	padding: 10px 20px;
	text-decoration: none;
	color:#000;
	margin-right: 10px;
	background: #fff;
	border-radius: 10px;
    display:inline-block;
	font-size: 22px;
    margin-bottom:15px
}

.thebox a > span {
background: #9CA1A4 none repeat scroll 0% 0%;
padding: 2px 5px;
font-size: 14px;
position: relative;
top: -2px;
color: #FFF;
margin-right: 5px;
border-radius: 15px;
}

.thebox a > i {
	font-size: 13px;
	position: relative;
	top: -3px;
	left: 10px;
	color: #ccc
}

.thebox a:hover {
	color: red;
	top:-20px;
}


.social_icons {
   position: absolute;
   list-style: none;
   bottom: 0
}

.social_icons li {
	display: inline;
	margin-right: 10px
}

.social_icons li a {
	color:#fff;
	padding: 3px 5px;
}

</style>

<body>
<div class="page">
<div class="thebox">
   
<h1>ORG Software Suite </h1>
<p style="margin-bottom: 45px;">KLAXYCOM</p>
<a style="display:none" href="?frontdesk"><span>ORG <i class="fa fa-male"></i></span> Frontdesk </a>
<a href="{{ route('pos') }}"><span>ORG <i class="fa fa-cutlery"></i></span> POS </a>
<a style="" onclick="JSObj.OpenExe('ORG Frontdesk',true);" href="#"><span>ORG <i class="fa fa-file-text"></i> </span> Frontdesk</a>
<a href="{{ route('pos') }}">Health Center </a>
<a href="{{ url('stock1', $parameters = array(), $secure = false) }}"><span>ORG <i class="fa fa-archive"></i></span> Stock </a>
<a style="color:red;" href="{{ route('backoffice') }}"><span>ORG <i class="fa fa-file-text"></i> </span> Back Office</a>

<p style="margin-top:80px;opacity:.5;font-size:12px;">&copy; www.klaxycom.com</p>
</div>


<ul class="social_icons">
<li><a href="#"><i class='fa fa-facebook'></i></a></li>
<li><a href="#"><i class='fa fa-twitter'></i></a></li>
<li><a href="#"><i class="fa fa-envelope"></i></a></li>
</ul>

</div>
</body>
</html>
