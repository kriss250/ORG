<?php $prop = \App\Resto::get()->first(); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
        <meta name="csrf-token" content="{{csrf_token() }}" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        {!! HTML::style('assets/css/vendor/bootstrap.min.css') !!}
        {!! HTML::style('assets/css/vendor/font-awesome.min.css') !!}
        {!! HTML::style('assets/js/vendor/datepicker/css/bootstrap-datepicker3.standalone.min.css') !!}
        {!! HTML::style('assets/js/vendor/daterangepicker/daterangepicker.css') !!}

    <!-- SCRIPTS -->
        {!! HTML::script('assets/js/vendor/jquery/jquery-1.11.2.min.js') !!}
        {!! HTML::script('assets/js/vendor/bootstrap/bootstrap.min.js') !!}


    <title>Admin | system diagnostics</title>

</head>
<body>
    @yield("contents")
</body>
</html>
