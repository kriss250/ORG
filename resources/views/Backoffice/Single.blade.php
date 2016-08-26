<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
        <meta name="csrf-token" content="{{csrf_token() }}" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        {!! HTML::style('assets/css/vendor/bootstrap.min.css') !!}
        {!! HTML::style('assets/css/vendor/font-awesome.min.css') !!}

    <!-- SCRIPTS -->
        {!! HTML::script('assets/js/vendor/jquery/jquery-1.11.2.min.js') !!}
        {!! HTML::script('assets/js/vendor/bootstrap/bootstrap.min.js') !!}
        {!! HTML::script('assets/js/vendor/jquery-ui/jquery-ui.min.js') !!}

        {!! HTML::script('assets/js/vendor/slimscroll/jquery.slimscroll.min.js') !!}
        {!! HTML::script('assets/js/vendor/daterangepicker/daterangepicker.js') !!}
        {!! HTML::script('assets/js/vendor/datepicker/js/bootstrap-datepicker.min.js') !!}


    <title></title>

</head>
<body>

    <style>
        body {
            background:#1f1f1f
        }
    </style>
    <button class="btn btn-primary">Print</button>
    <div style="max-width:1000px;margin:auto;background:#fff;">
        @yield("contents")
    </div>

</body>
</html>
