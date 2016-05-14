<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=10" >

        {!! HTML::style('assets/css/vendor/bootstrap.min.css') !!}
        {!! HTML::style('assets/css/vendor/font-awesome.min.css') !!}
        {!! HTML::style('assets/css/vendor/jquery-ui.min.css') !!}
        {!! HTML::style('assets/css/vendor/fullcalendar.css') !!}
        {!! HTML::style('assets/css/vendor/fullcalendar.min.css') !!}
        
        {!! HTML::style('assets/css/Frontdesk-homeview.css') !!}
    
    <!-- SCRIPTS -->
        {!! HTML::script('assets/js/vendor/jquery/jquery-1.11.2.min.js') !!}
        {!! HTML::script('assets/js/vendor/moment/moment.min.js') !!}
        {!! HTML::script('assets/js/vendor/jquery-ui/jquery-ui.min.js') !!}
        {!! HTML::script('assets/js/vendor/bootstrap/bootstrap.min.js') !!}
        {!! HTML::script('assets/js/vendor/calendar/fullcalendar.min.js') !!}
        {!! HTML::script('assets/js/vendor/calendar/gcal.js') !!}
        {!! HTML::script('assets/js/ORGFrontdesk.js') !!}
    
    <title></title>
</head>
<body class="noselect">

    <div class="contents">
        @yield("contents")
    </div>



        
</body>
</html>
