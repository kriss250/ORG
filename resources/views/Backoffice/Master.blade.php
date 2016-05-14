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
        {!! HTML::script('assets/js/backoffice.js') !!}

    
    <title>ORG Backoffice</title>

</head>
<body>
<style type="text/css">
    .input-group .twitter-typeahead {
        display: block !important;
    }

    .tt-suggestion {
        padding: 10px 0px;
        cursor: pointer;
    }
    .tt-menu {
        position: absolute;
        top: 32px !important;
        left: 0px;
        z-index: 100;
        display: block;
        background: rgb(255, 255, 255) none repeat scroll 0% 0%;
        width: 100%;
        padding: 11px;
        border: 1px solid rgb(204, 204, 204);
    }
</style>
<script type="text/javascript">

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
                url: "{{ url('/Backoffice/Reports/Header') }}?title="+title+"&date_range="+dates,
                type: "get",
                success:function(data)
                {
                    $(headerDiv).html(data);
                    $(".main-contents").prepend($(headerDiv));
                    window.print();
                   
                }
            })
           
        })

        //End report printing

    var searchDiv = $(".search-results");
    var searchList = $("<ul class='search-results-list'>");

    $("#master-search").blur(function () {
        setTimeout(function () {
            $(searchDiv).html($(searchList)).removeClass("open");
            $(".loader-img").removeClass("shown");
        }, 1500);
        
    })

    $("#master-search").keyup(function () {
        $(".loader-img").addClass("shown");
        var q = $(this).val();
        var url = "{{route('BackofficeSearch')}}";
        url = url.replace('%7Bquery%7D', q);
        
        $.ajax({
            url: url,
            type: "get",
            contentType: "json",
            success : function(data)
            {
                data = JSON.parse(data);
                $(searchList).html("");
                $.each(data, function (e, v) {
                    var uri = "{{url('/Backoffice/itemPreview') }}/?type=" + v.location.split('=')[1]+"&id="+v.ID;
                  

                    $(searchList).append("<li><a data-height='600' data-width='520' class='modal-btn' href='"+uri+"'>" + v.text + 
                    	"<span>Found in <em>"+v.location.split('=')[1]+"</em></span></a></li>");
                });

                $(searchDiv).html($(searchList)).addClass("open");
                $(".loader-img").removeClass("shown");
            }
        })
        
    });

})

</script>

<?php

$announcements = \DB::connection("mysql_backoffice")->select("select idannouncements,title,body,concat_ws(' ',firstname,lastname) as user,announcements.date,user_seen_annoucement.date from announcements join org_pos.users on users.id =announcements.user_id left join user_seen_annoucement on idannouncements=announcement_id and user_seen_annoucement.user_id=? where user_seen_annoucement.date is null and announcements.user_id<>?",[\Auth::user()->id,\Auth::user()->id]);
?>

@if(count($announcements)>0)
    
    <div class="announcements">
     <script type="text/javascript">
     $(document).ready(function(){

        @foreach($announcements as $announcement)

       
            bootbox.dialog({
              message: "{{ trim($announcement->body) }} <br><br><p><b>From : {{ $announcement->user }} </b></p>",
              title: "{{ $announcement->title }}",
              buttons: {
                success: {
                  label: "OK!",
                  className: "btn-success",
                  callback: function() {
                     $.get( "{{ action('AnnouncementController@index') }}?markasseen=1&announcement={{$announcement->idannouncements}}");
                  }
                }
              }
          });
        
        @endforeach
        })
        </script>
    </div>
    
@endif 
    <header id="header">
        <div class="grid container-fluid">
            <div class="col-md-1 logo">
                <img src="/assets/images/backoffice_logo.png" width="45" />
            </div>

            <div style="padding-left:5px;" class="col-md-5">
                <h4 style="font-family:Lato;margin-bottom:0;margin-top:5px;">Classic Hotel</h4>
                <p style="color:rgb(182, 179, 179)">Backoffice | ORG Systems</p>
            </div>

            <div class="col-md-6 header-menu">
                <ul>
                    <li class="user-item">
                       <span class="round"><i class="fa fa-user"></i></span>
                        <div class="btn-group">
                              <button type="button" style="font-size:12px;background:rgb(91, 183, 63);border: medium none;border-radius: 0px 4px 4px 0;margin-left: -10px;margin-top: 12px;color: rgba(255, 255, 255, 0.93);padding-left: 15px;" class="btn btn-default dropdown-toggle login-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Hi, {{ \Auth::user()->username }} <span class="caret"></span>
                              </button>
                              <ul class="dropdown-menu">
                                <li><a class="modal-btn" data-width="400" data-height="300" href="/Backoffice/Settings/changePassword"><i class="fa fa-key"></i> Change Password</a></li>
                                <li><a href="/POS/logout"> <i class="fa fa-sign-out"></i> Logout</a></li>
                              </ul>
                         </div>
                    </li>

                    <li>
                        <a class="expand-btn" href="#"><i class="fa fa-arrows-alt"></i></a>
                    </li>
                    <li>
                        <?php $tsp = strtotime( \ORG\Dates::$RESTODT); ?>
                        <p style="padding-top: 16px;padding-right: 30px;font-size: 11px;color: rgb(111, 111, 111)"> {{ date("l d, m Y",$tsp) }}</p>
                    </li>
                </ul>
            </div>
            
        </div>
    </header>

    <div class="grid">

        <div class="row subheader">
            <div class="col-md-3">
                <h4 style="margin-top: 15px; margin-bottom: 0; margin-left: 6px; color: rgb(93, 93, 93)">BACKOFFICE </h4>
                <span style="font-size: 11px;margin-left:6px;color:rgb(132, 132, 132)">Dashboard</span>
            </div>

            <div class="col-md-6">
                <br />
                <form class="search-form" action="">
                    <div class="input-group">
                          <input id="master-search" autocomplete="off" placeholder="Search ...." type="text" class="typeahead form-control" /> 
                          <span class="input-group-addon"><i class="fa fa-search"></i></span>
                     </div>
                    <img class="loader-img" src="/assets/images/small-loader.gif" />
                    <div class='search-results closed'></div>
                </form>
            </div>
            <div class="col-md-3">
                <ul class="inline-list notification-list">
                    <li>Notifications</li>
                    <li><a href=""><i class="fa fa-bell"></i></a> </li>
                    <li><a href=""><i class="fa fa-exclamation-triangle"></i></a></li>
                </ul>
            </div>
        </div>

        <div class="contents">
            <div class="row">

                <div class="col-md-2 sidebar">
                    <p>Menu</p>
                    <ul>
                        <li><a href="{{ action("BackofficeController@index") }}"><i class="fa fa-tachometer"></i>Dashboard</a></li>
                        <li><a class="dropdown-btn" href=""><i class="fa fa-bullhorn"></i> Announcements <i class="fa fa-chevron-down"></i></a>
                             <ul class="dropdown-menu">
                                 <li><a class="modal-btn" data-width="350" data-height="250" href="{{ action("AnnouncementController@create") }}">New Announcement</a></li>
                                 <li><a class="modal-btn" data-width="650" data-height="380" href="{{ action("AnnouncementController@index") }}">List Announcement</a></li>
                             </ul>
                        </li>
                   
                        <li><a class="dropdown-btn" href=""><i class="fa fa-money"></i> Debts <i class="fa fa-chevron-down"></i></a>
                             <ul class="dropdown-menu">
                                 <li><a  href="{{ action("POSCreditController@unexportedDebts") }}">Unexported Debts</a></li>
                                 <li><a  href="{{ action("POSCreditController@externalDebts") }}">External Debts</a></li>
                                 <li><a  href="{{ action("POSCreditController@internalDebts") }}">Internal Debts</a></li>
                             </ul>
                        </li>
                        
                        <li><a href="{{ action("PaymentsController@index") }}"><i class="fa fa-money"></i> Payments</a></li>
                        <li class="report-btn"><a class="dropdown-btn" href=""><i class="fa fa-cutlery"></i> POS Reports <i class="fa fa-chevron-down"></i></a>
                                @include("layouts.pos_menu")
                        </li>
                        <li class="report-btn"><a class="dropdown-btn" href="#"> <i class="fa fa-bed"></i> Frontdesk Reports <i class="fa fa-chevron-down"></i></a>
                            @include("layouts.frontdesk_menu")
                        </li>

                         <li class="report-btn"><a href="{{ action("BackofficeReportController@index",'cashBooks') }}"><i class="fa fa-file-text-o"></i> CashBook Reports</a></li>
                       

                         <li class="report-btn"><a class="dropdown-btn" href="#"> <i class="fa fa-archive"></i> Stock Reports <i class="fa fa-chevron-down"></i></a>
                            <ul class="dropdown-menu">
                                <li><a  href="{{ action("BackofficeReportController@index",'stockOverview') }}">Stock Overview</a> </li>
                                <li><a  href="{{ action("BackofficeReportController@index",'purchases') }}">Purchases</a> </li>
                                <li><a  href="{{ action("BackofficeReportController@index",'stockSales') }}">Sales</a> </li>
                                <li><a  href="{{ action("BackofficeReportController@index",'stockRequisition') }}">Requisition</a> </li>
                                <li><a  href="{{ action("BackofficeReportController@index",'damagedProducts') }}">Damaged Products</a> </li>

                            </ul>
                        </li>
                        @if(\Auth::user()->level > 9)
                        <li><a class="dropdown-btn" href=""><i class="fa fa-user"></i> User Management <i class="fa fa-chevron-down"></i></a>
                        	  <ul class="dropdown-menu">
                                <li><a class="modal-btn" data-width="450" data-height="650"  href="{{ action("UsersController@create") }}">New User</a> </li>

                                <li><a class="modal-btn" data-width="400" data-height="550"  href="{{ action("WaiterController@create") }}">New Waiter</a> </li>

                                <li><a  href="#">User List</a> </li>
                                
                            </ul>
                        </li>
                        @endif
                        <!-- <li><a href=""><i class="fa fa-cog"></i> Settings</a></li> -->
                    </ul>
                    <ul class="menu2">
                        <li><a href="{{ url('POS', $parameters = array(), true) }}">POS</a></li>
                        <li><a href="{{ url('stock1', $parameters = array(), $secure = false) }}">Stock</a></li>
                    </ul>
                 
                </div>

                <div class="col-md-10">
                    <div class=" main-contents">
                        @yield("contents")
                        @include("Backoffice.Reports.reportFooter")
                    </div>
                </div>
            </div>
            
        </div>
    </div>

   <footer class="footer">
        <div class="grid">
            <p class="text-center">&copy; {{ date('Y') }} Classic Hotel</p>
            <p class="text-center"> ORG System </p>
        </div>
   </footer>
</body>
</html>