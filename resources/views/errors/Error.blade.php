<!DOCTYPE html>
<html>
    <head>
        {!! HTML::style('assets/css/vendor/bootstrap.min.css') !!}
        {!! HTML::style('assets/css/vendor/font-awesome.min.css') !!}
        {!! HTML::script('assets/js/vendor/jquery/jquery-1.11.2.min.js') !!}
        <title>ORG Error</title>
    </head>
<body>
    <style>
        body {
            background: #f6f6f6;
        }

        .wrapper {
            margin: 35px auto;
            width: 80%;
            max-width: 750px;
            text-align: center;
            box-shadow: 0 0 25px #a9a9a9;
            padding: 35px 20px;
            border-radius: 10px;
            background: #fff;
        }

            .wrapper .fa {
                font-size: 40px;
                color: #d70000;
            }

            .hidden-error {
                display:none
            }
    </style>
    <div class="wrapper">
        <i class="fa fa-exclamation-triangle"></i>
        <h3>An error occurred While performing the desired operation</h3>
        <p style="opacity:.8">
            Please contact your system administrator for further investigation
            <span onclick="showError();" style="cursor:pointer; border-bottom:1px dotted;opacity:.5">#Trace</span>
        </p>
        <p class="hidden-error text-danger">{{$e}} in {{$ex->getFile()}}</p>
        <a href="{{URL::previous()}}" style="margin-top:15px;" class="btn btn-primary">GO BACK</a>
    </div>

    <script>
        function showError()
        {
            $(".hidden-error").toggle();
        }

        $(document).ready(function () {
            $.ajaxSetup({ async: true });
            var msg = `{!!$ex->getMessage()." on Line # ".$ex->getLine()." in ".$ex->getFile()." ({$ex->getCode()})
                ----------------------------------------------------------------------------------------------------
                ".$ex->getTraceAsString()!!}`;
            $.post('{{url("/errors/report")}}', {"msg":msg,'_token':'{{csrf_token()}}'});
        })
    </script>

</body>
</html>
