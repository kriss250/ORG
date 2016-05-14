<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    {!! HTML::style('assets/css/vendor/bootstrap.min.css') !!}
    {!! HTML::style('assets/css/vendor/font-awesome.min.css') !!}
    {!! HTML::style('assets/js/vendor/chosen/chosen.css') !!}
    {!! HTML::style('assets/js/vendor/datatables/css/jquery.dataTables.min.css') !!}
    {!! HTML::style('assets/js/vendor/datatables/extensions/TableTools/css/dataTables.tableTools.min.css') !!}
    {!! HTML::style('assets/css/vendor/jquery-ui.min.css') !!}
    {!! HTML::style('assets/js/vendor/datepicker/css/bootstrap-datepicker3.standalone.min.css') !!}
    {!! HTML::style('assets/js/vendor/daterangepicker/daterangepicker.css') !!}
    {!! HTML::style('assets/css/Backoffice.css') !!}

    <!-- SCRIPTS -->
    {!! HTML::script('assets/js/vendor/jquery/jquery-1.11.2.min.js') !!}
    {!! HTML::script('assets/js/vendor/bootstrap/bootstrap.min.js') !!}
    {!! HTML::script('assets/js/vendor/jquery-ui/jquery-ui.min.js') !!}
    {!! HTML::script('assets/js/vendor/moment/moment.min.js') !!}
    {!! HTML::script('assets/js/vendor/datatables/js/jquery.dataTables.min.js') !!}
    {!! HTML::script('assets/js/vendor/chosen/chosen.jquery.min.js') !!}
    {!! HTML::script('assets/js/vendor/datatables/extensions/TableTools/js/dataTables.tableTools.min.js') !!}
    {!! HTML::script('assets/js/vendor/accounting/accounting.js') !!}
    {!! HTML::script('assets/js/vendor/highcharts/highcharts.js') !!}
    {!! HTML::script('assets/js/vendor/typeahead/typeahead.bundle.min.js') !!}
    {!! HTML::script('assets/js/vendor/slimscroll/jquery.slimscroll.min.js') !!}
    {!! HTML::script('assets/js/vendor/daterangepicker/daterangepicker.js') !!}
    {!! HTML::script('assets/js/vendor/datepicker/js/bootstrap-datepicker.min.js') !!}
    {!! HTML::script('assets/js/vendor/bootbox/bootbox.min.js') !!}
    {!! HTML::script('assets/js/pos.jquery.js') !!}
    {!! HTML::script('assets/js/backoffice.js') !!}


    <title>ORG Frontdesk Reports</title>

</head>
<body>
    <script>

    $(document).ready(function () {

        //printing report
        $(".report-print-btn").click(function (e) {
            e.preventDefault();
            var headerDiv = $("<div class='print-header'>");
            var footerDiv = $("<div class='print-footer'>");
            var ds = "";
            var title = $(this).attr("data-title");
            var dates = $(this).attr("data-dates");
            $.ajax({
                url: "/Backoffice/Reports/Header?title="+title+"&date_range="+dates,
                type: "get",
                success:function(data)
                {
                    $(headerDiv).html(data);
                    $("body").prepend($(headerDiv));
                    window.print();
                }
            })

        })

        //End report printing


})

    </script>
    <style>
    .report-page-header {
    position: fixed;
    left: 0;
    right: 0;
    background: linear-gradient(to top,#f3f3f3,#fff);
    height: 55px;
    z-index: 55;
    top: 0;
    padding: 3px 25px;
    border-bottom: 1px solid #dedede;
    }

    .report-page-header h2 {
        font-size: 24px;
        margin-bottom:0;
    margin-top: 3px;
    }

    .report-page-header p {
        font-size: 10px;
        color:#ccc;
    }

    .report-page-content {
    margin-top: 5px;
    position:relative;
    top:70px
    }

    .menu-wrapper {
    background: rgb(255, 255, 255) none repeat scroll 0% 0%;
    padding: 13px;
    position: relative;
    margin-top: 20px;
    border: 1px solid rgb(108, 188, 120);
    }

    .menu-wrapper ul {
    padding: 0;
    padding-left: 18px;
    }

    .menu-wrapper:before {
    content: "Reports";
    position: absolute;
    top: -20px;
    text-align: center;
    padding: 4px;
    background: rgb(62, 152, 76);
    color:#fff;
    left: -1px;
    right: -1px;
    }

    .report-page-content .page-contents {
        border:1px solid rgb(215, 215, 215)
    }
    </style>
    <div class="report-page-header">
        <div class="row">
            <div class="col-md-10">
                <h2>
                    Frontdesk
                    <span style="font-size:14px">Reports</span>
                </h2>
                <p>ORG SYSTEMS</p>
            </div>

            <div class="col-md-2 text-right" style="color:#c6c6c6;font-size:11px">
            <i class="fa fa-info-circle" style="position:relative;top:10px;padding-right:15px; font-size:18px;"></i> 
            Reporting, Loggedin as <br />
            {{ Auth::user()->username }}</div>
        </div>
    </div>


    <div class="report-page-content">

        <div class="col-md-2">
            <div class="menu-wrapper">
                @include("/layouts/frontdesk_menu",["_imported"=>1])
            </div>
        </div>

        <div class="col-md-10">
            @yield("contents")
        </div>

    </div>
</body>
</html>
