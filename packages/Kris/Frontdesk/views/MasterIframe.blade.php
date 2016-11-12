<!DOCTYPE html>
<html class="ifr-html" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0,target-densitydpi=device-dpi, user-scalable=no" />
    {!!HTML::style("assets/css/vendor/bootstrap.min.css")!!}
    {!!HTML::style("assets/css/vendor/font-awesome.min.css")!!}
    {!!HTML::style("assets/css/vendor/jquery-ui.min.css")!!}
    {!!HTML::style("assets/js/vendor/bsdatepicker/css/bootstrap-datepicker.min.css")!!}
    {!!HTML::style("assets/css/frontdesk.css")!!}
    {!!HTML::style("assets/css/print-doc.css")!!}

    {!!HTML::script("assets/js/vendor/jquery/jquery-1.11.2.min.js")!!}
    {!!HTML::script("assets/js/vendor/bootstrap/bootstrap.min.js")!!}

    {!!HTML::script("assets/js/vendor/moment/moment.min.js") !!}

    {!!HTML::script("assets/js/vendor/chosen/chosen.jquery.min.js")!!}
    {!!HTML::script("assets/js/vendor/jquery-ui/jquery-ui.min.js") !!}
    {!!HTML::script("assets/js/vendor/bsdatepicker/js/bootstrap-datepicker.min.js") !!}
    {!!HTML::script('assets/js/vendor/highcharts/highcharts.js') !!}
    {!!HTML::script("assets/js/vendor/slimscroll/jquery.slimscroll.min.js") !!}
    {!!HTML::script('assets/js/fx.js') !!}
    {!!HTML::script("assets/js/fo-main.js")!!}
    <title>Frontoffice</title>
</head>

<body class="ifr-body">
    <style>
        fieldset {
            border:1px solid #d9d9d9
        }
    </style>

    <script>
        function openRoom(reservationid, src) {
            var uri = '{{action("\Kris\Frontdesk\Controllers\OperationsController@roomView","__id__")}}';
            uri = uri.replace("__id__", reservationid);
            window.openDialog(uri, 'Room', 'width=800,height=590,resizable=no', src);
        }


        function openWindow(name,_title,src,_width,_height)
        {
            var url  = '{{action("\Kris\Frontdesk\Controllers\OperationsController@frame",'__name__')}}';
            url = url.replace("__name__",name);
            var width = 800;
            var height=590;
            var title = "";

            width =  typeof _width == "undefined" ? width : _width;
            height =  typeof _height == "undefined" ? height : _height;
            title = typeof _title == "undefined" ? title : _title;

            openDialog(url,title,'width='+width+',height='+height,src);
        }

        $(document).ready(function () {

          $(".suggest-input").suggest({
            url : '{{action("SuggestionsController@index")}}'
          });
            $(".charges-table-wrapper").slimscroll({
                height: "182px",
                alwaysVisible: false,
                railVisible: true
            });
        })
    </script>

    @yield("contents")

</body>
</html>
