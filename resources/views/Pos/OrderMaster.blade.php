<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{csrf_token() }}" >

        {!!HTML::style('assets/css/vendor/bootstrap.min.css') !!}
        {!!HTML::style('assets/css/vendor/font-awesome.min.css') !!}
        {!!HTML::style('assets/js/vendor/chosen/chosen.css') !!}
        {!!HTML::style('assets/js/vendor/datatables/css/jquery.dataTables.min.css') !!}
        {!!HTML::style('assets/js/vendor/datatables/extensions/TableTools/css/dataTables.tableTools.min.css') !!}
        {!!HTML::style('assets/js/vendor/datepicker/css/bootstrap-datepicker3.standalone.min.css') !!}
        {!!HTML::style('assets/css/vendor/jquery-ui.min.css') !!}
        {!!HTML::style('assets/js/vendor/datetimepicker/css/bootstrap-datetimepicker.min.css') !!}
        {!!HTML::style('assets/css/POS.css') !!}

        {!!HTML::style('assets/css/'.(\Session::get("pos.mode")).'.css')!!}
        {!!HTML::style('assets/css/touch.css') !!}
        {!!HTML::style('assets/css/orders.css') !!}

    <!-- SCRIPTS -->
        {!!HTML::script('assets/js/vendor/jquery/jquery-1.11.2.min.js') !!}
        {!!HTML::script('assets/js/vendor/bootstrap/bootstrap.min.js') !!}
        {!!HTML::script('assets/js/vendor/jquery-ui/jquery-ui.min.js') !!}
        {!!HTML::script('assets/js/vendor/moment/moment.min.js') !!}
        {!!HTML::script('assets/js/vendor/datatables/js/jquery.dataTables.min.js') !!}
        {!!HTML::script('assets/js/vendor/chosen/chosen.jquery.min.js') !!}
        {!!HTML::script('assets/js/vendor/datatables/extensions/TableTools/js/dataTables.tableTools.min.js') !!}
        {!!HTML::script('assets/js/vendor/accounting/accounting.js') !!}
        {!!HTML::script('assets/js/vendor/datetimepicker/js/bootstrap-datetimepicker.min.js') !!}
        {!!HTML::script('assets/js/vendor/datepicker/js/bootstrap-datepicker.min.js') !!}
        {!!HTML::script('assets/js/pos.jquery.js') !!}
        {!!HTML::script('assets/js/POS.js') !!}

    
    <title>ORG POS </title>
</head>
<body class="noselect">
    <div class="print_container"></div>


    @if(isset($errors) && count($errors) > 0)
    <div style="background: rgb(192, 57, 43) none repeat scroll 0% 0%; color: rgb(0, 0, 0);" class="ualert ui-draggable ui-draggable-handle">
        <i class="fa fa-exclamation-triangle"></i><div class="inner_content">

            {{$errors->first() }}
        </div><button class="ok-btn ht_close">OK</button>
    </div>
    @endif

    <div class="header">
        <div class="grid">
            <div class="row">
                <div class="col-md-3 col-xs-3">
                    <h4 style="text-transform:uppercase;font-family:'Open Sans';opacity:.7;padding-left:20px">
                        Captain Order
                        <span style="display:block;opacity:.5;font-size:11px">ORG Point of sales</span>
                    </h4>
                </div>

                <div class="col-md-9 col-xs-9 header-right">

                    <ul>
                        <li>

                            <span class="clock">
                                <i class="fa fa-clock-o"></i>{{date('l d, m Y',strtotime(\ORG\Dates::$RESTODT)) }}
                                <i class="time">{{date('H:i') }}</i>
                            </span>
                        </li>

                        <li>
                            <a href="{{route('pos') }}" class="btn btn-xs btn-danger">
                                <i class="fa fa-times"></i> EXIT
                            </a>
                        </li>

                       



                    </ul>
                </div>
            </div>

        </div>
    </div>
    <div class="grid main-container">
        <div class="row">
            <div class="mini-submenu">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </div>

            <div class="contents col-md-9 col-lg-9">
                @yield("printHeader")
                @yield("contents")
            </div>
        </div>
    </div>


</body>
</html>
