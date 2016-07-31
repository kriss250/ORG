<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0,target-densitydpi=device-dpi, user-scalable=no" />
    {!!HTML::style("assets/css/vendor/bootstrap.min.css")!!}
    {!!HTML::style("assets/css/vendor/font-awesome.min.css")!!}
    {!!HTML::style("assets/css/vendor/jquery-ui.min.css")!!}
   
    {!!HTML::style("assets/js/vendor/datetimepicker/css/bootstrap-datetimepicker.min.css")!!}
    {!!HTML::style("assets/css/floorview.css")!!}
    {!!HTML::style("assets/css/frontdesk.css")!!}

    {!!HTML::script("assets/js/vendor/jquery/jquery-1.11.2.min.js")!!}
    {!!HTML::script("assets/js/vendor/bootstrap/bootstrap.min.js")!!}

    {!!HTML::script("assets/js/vendor/moment/moment.min.js") !!}

    {!!HTML::script("assets/js/vendor/chosen/chosen.jquery.min.js")!!}
    {!!HTML::script("assets/js/vendor/jquery-ui/jquery-ui.min.js") !!}
    {!!HTML::script("assets/js/vendor/datetimepicker/js/bootstrap-datetimepicker.min.js") !!}
    {!!HTML::script("assets/js/vendor/slimscroll/jquery.slimscroll.min.js") !!}

   
    {!!HTML::script("assets/js/fo-main.js")!!}
    <title>Frontoffice</title>
</head>

<body class="site">
    <script type="text/javascript">
        $(document).ready(function () {
            $(".window").draggable({handle:'.title-bar'});
        })

        function openRoom(reservationid,src)
        {
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
    </script>
    <?php
    $wdate = \Kris\Frontdesk\Env::WD();
    ?>
    <header id="header">
        <div class="grid">

            <div class="row">
                <div class="col-md-3 title-wrapper">
                    <div class="col-md-3 logo-wrapper">
                        <img src="/assets/images/backoffice_logo.png" width="30" />
                    </div>

                    <div class="col-md-9">
                        <p>ORG Frontdesk</p>
                        <p style="margin-top:-4px">Version 1.0</p>
                    </div>
                </div>

                <div class="col-md-7 text-right">

                    Arrival <span class="text-center circle-badge" style=" background:#e74c3c;">3</span>
                    Departure <span class="text-center circle-badge" style=" background:#9b59b6;">3</span>
                </div>
                <div class="col-md-2" style="text-align:right">

                </div>

            </div>

        </div>
    </header>

    <nav class="main-menu">
        <div class="menu-1">
        <ul class="grid nav nav-tabs">

                <li class="active">
                    <a data-toggle="tab" href="#pane-1">Home </a>
                </li>

                <li class="view-switch">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        Views <i class="fa fa-angle-down" aria-hidden="true"></i>

                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{action("\Kris\Frontdesk\Controllers\OperationsController@home","standard")}}"><i class="fa fa-newspaper-o" aria-hidden="true"></i> Standard View</a> </li>
                        <li><a href="{{action("\Kris\Frontdesk\Controllers\OperationsController@home","booking")}}?startdate={{\Kris\Frontdesk\Env::WD()->format('Y-m-d')}}&days=20"><i class="fa fa-tasks"></i> Booking View</a> </li>
                        <li><a href="{{action("\Kris\Frontdesk\Controllers\OperationsController@home","floor")}}"><i class="fa fa-braille" aria-hidden="true"></i>
Floors View</a> </li>
                    </ul>
                </li>

                <!--<li>
                    <a href="">Customers</a>
                </li>-->


                <li>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">Housekeeping <i class="fa fa-angle-down" aria-hidden="true"></i></a>
                    <ul class="dropdown-menu">
                        <li><a onclick="openWindow('newHkTask','Housekeeping Task',this,760,480)" href="#">Today's Housekeeping</a></li>

                        <li class="separator"></li>
                        <li><a onclick="openWindow('newLaundry','Laundry Order',this,480,310)" href="#">New Laundry Order</a></li>
                        <li><a href="#">Laundry Orders</a></li>
                        <!--<li><a href="#">Lost and found</a></li>-->
                    </ul>
                </li>


                <li>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="$">Charts <i class="fa fa-angle-down" aria-hidden="true"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="#">Occupancy</a></li>
                    </ul>
                </li>
                <li>
                    <a data-toggle="tab" href="#pane-2">Reports <i class="fa fa-angle-down"></i></a>
                    
                </li>

                <li>
                    <a href=""><i style="color:rgb(144, 192, 203)" class="fa fa-moon-o"></i> New Day</a>
                </li>

                <div class="notification-menu">
                    System Date : <span style="font-weight: bold !important;background: rgba(236, 240, 241, 0.86) none repeat scroll 0% 0%;
padding: 2px 6px;
border-radius: 3px;
color: rgb(43, 92, 134);
margin-right: 8px;">
                        <i class="fa fa-sun-o"></i>
                        {{\Kris\Frontdesk\Env::WD(true)}}
                    </span>
                    Arrival <span class="text-center circle-badge" style=" background:#e74c3c;">3</span>
                    Departure <span class="text-center circle-badge" style=" background:#9b59b6;">3</span>
                    <li style="margin:0;padding:0">
                        <a style="position:relative;margin-bottom:0;margin-top:0" href="#" data-toggle="dropdown" class="btn btn-primary dropdown-toggle" type="button">
                            <i class="fa fa-user"></i> {{\Kris\Frontdesk\User::me()->username}} <span class="caret"></span>

                        </a>

                        <ul class="dropdown-menu">
                            <li><a href="{{action('\Kris\Frontdesk\Controllers\UsersController@logout')}}"><i class="fa fa-sign-out"></i> Logout</a> </li>
                        </ul>
                    </li>
                </div>

               
         

        </ul>
            <div class="clearfix"></div>
         </div>
        <div class="tab-content">

            <div class="tab-pane active" id="pane-1">
                <div class="grid">
                    <ul class="menu-2"> 

                        <li class="menu-group">
                            <ul>
                                <li>
                                    <a data-desc="Use this window to register a client that is checking in a to this date , without a reservation" class="dlg-bt" title="Guest Walkin" onclick="window.openDialog('http://org.com/fo/ajax/form/walkin','Room','width=800,height=590,resizable=no',this)" data-url="" href="#">
                                        <img src="/images/frontdesk/man-suit.svg" />
                                        Walkin
                                    </a>
                                </li>

                                <li>

                                    <a data-desc="Use this window to make a reservation in future dates" onclick="window.openDialog('http://org.com/fo/ajax/form/reservation','Room','width=800,height=590,resizable=no',this)" href="#">
                                        <img src="/images/frontdesk/calendar-plus.svg" />
                                        New Reservation
                                    </a>
                                </li>

                                <li>
                                    <a data-desc="Use this window to make a group reservation" class="dlg-btn" title="Group reservation" data-toggle="modal" onclick="openDialog('http://org.com/fo/ajax/form/groupReservation','Reservation','width=800,height=590,resizable=no',this)" href="">
                                        <img height="32" src="/images/frontdesk/groupres.png" />
                                        Group Reservation
                                    </a>
                                </li>

                                <li>
                                    <a data-iframe="yes" data-desc="Use this window to browse reservations , based on status and arrival dates" title="Reservations" onclick="openDialog('http://org.com/fo/ajax/list/reservation','Reservation','width=800,height=590,resizable=no',this)" href="#">
                                        <img src="/images/frontdesk/file-picture.svg" />
                                        Reservation List
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="menu-group">
                            <ul>
                                <li>
                                    <a data-iframe="yes" data-desc="Use this window to browse reservations , based on status and arrival dates" title="Reservations" onclick="openWindow('expectedArrival','Reservation')" href="#">
                                        <img src="/images/frontdesk/arrow-right.svg" />
                                        Expected Arrival
                                    </a>
                                </li>

                                <li>
                                    <a data-iframe="yes" data-desc="Use this window to browse reservations , based on status and arrival dates" class="dlg-btn" title="Reservations" onclick="openDialog('http://org.com/fo/section/frame/expectedDeparture','Reservation','width=800,height=590,resizable=no',this)" href="#">
                                        <img src="/images/frontdesk/arrow-left.svg" />
                                        Expected Departure
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="menu-group">
                            <ul>
                                <li>
                                    <a data-iframe="yes" data-desc="Use this window to browse reservations , based on status and arrival dates" class="dlg-btn" title="Bills" data-toggle="modal" onclick="openDialog('http://org.com/fo/section/frame/billList','Reservation','width=800,height=590,resizable=no',this)" href="#">
                                        <img src="/images/frontdesk/file-attachment.svg" />
                                        Bills
                                    </a>
                                </li>

                                <li>
                                    <a data-iframe="yes" data-desc="Use this window to browse reservations , based on status and arrival dates" class="dlg-btn" title="Reservations" data-toggle="modal" onclick="openDialog('http://org.com/fo/section/frame/statements','Reservation','width=800,height=590,resizable=no',this)" href="#">
                                        <img src="/images/frontdesk/file-graphic.svg" />
                                        Statement
                                    </a>
                                </li>
                            </ul>

                        </li>

                        <li style="padding:1px 15px" class="menu-group">
                            <ul>
                                <li>
                                    <a data-iframe="yes" data-desc="Manage room status" class="dlg-btn" title="Room status management" data-toggle="modal" onclick="openDialog('http://org.com/fo/section/frame/roomStatus','Reservation','width=850,height=610,resizable=no',this)" href="#">
                                        <img src="/images/frontdesk/hue.svg" />
                                        Room Status
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="menu-group">
                            <ul>
                                <li>
                                    <a data-iframe="yes" data-desc="Use this window to browse reservations , based on status and arrival dates" class="dlg-btn" title="Reservations" data-toggle="modal" onclick="openDialog('http://org.com/fo/section/frame/guestDB','Reservation','width=850,height=590,resizable=no',this)" href="#">
                                        <img src="/images/frontdesk/books-2.svg" />
                                        Guest Info.
                                    </a>
                                </li>

                                <li>
                                    <a data-iframe="yes" data-desc="Use this window to browse reservations , based on status and arrival dates" class="dlg-btn" title="Reservations" data-toggle="modal" onclick="openDialog('http://org.com/fo/section/frame/companies','Reservation','width=800,height=590,resizable=no',this)" href="#">
                                        <img src="/images/frontdesk/organogram-2.svg" />
                                        Companies
                                    </a>
                                </li>

                            </ul>
                        </li>

                        <li class="menu-group">
                            <ul>
                                <li>
                                    <a data-iframe="yes" data-desc="Use this window to browse reservations , based on status and arrival dates" class="dlg-btn" title="Reservations" data-toggle="modal" onclick="openDialog('http://org.com/fo/section/frame/banquet','Reservation','width=800,height=590,resizable=no',this)" href="#">
                                        <img src="/images/frontdesk/stopwatch.svg" />
                                        Banquet
                                    </a>
                                </li>

                                <li>
                                    <a data-iframe="yes" data-desc="List of all extra sales" class="dlg-btn" title="Sales" data-toggle="modal" onclick="openDialog('http://org.com/fo/section/frame/salesList?startdate=2016-07-17&enddate=2016-07-17','Sales List','width=620,height=490,resizable=no',this)" href="#">
                                        <img src="/images/frontdesk/basket-full.svg" />
                                        Other Sales List
                                    </a>
                                </li>
                                <li>
                                    <a data-iframe="yes" data-desc="Use this window to browse reservations , based on status and arrival dates" class="dlg-btn" title="Reservations" data-toggle="modal" onclick="openDialog('http://org.com/fo/section/frame/addSale','Extra Sales','width=480,height=540,resizable=no',this)" href="#">
                                        <img src="/images/frontdesk/cart-plus.svg" />
                                        Add Sales
                                    </a>
                                </li>
                            </ul>
                        </li>

                    </ul>
                    <div class="clearfix"></div>
                </div>
            </div>

            <div class="tab-pane" id="pane-2">
                <div style="padding-top:8px;" class="grid">
                <ul class="menu-2">

                    <li>
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <img src="/images/frontdesk/box-file.svg" />
                            Daily Reports
                            <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\ReportsController@index","frontofficeControl") }}','','width=1010,height=640',this)">Frontoffice Control</a>
                            </li>


                            <li>
                                <a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\ReportsController@index","frontofficeArrival") }}','','width=1010,height=640',this)">Arrival</a>
                            </li>


                            <li>
                                <a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\ReportsController@index","frontofficeExpectedArrival") }}','','width=1010,height=640',this)">Expected Arrival</a>
                            </li>


                            <li>
                                <a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\ReportsController@index","frontofficeDeparture") }}','','width=1010,height=640',this)">Departure</a>
                            </li>

                            <li>
                                <a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\ReportsController@index","frontofficeExpectedDeparture") }}','','width=1010,height=640',this)">Expected Departure</a>
                            </li>

                            <li>
                                <a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\ReportsController@index","frontdeskServiceSales") }}','','width=1010,height=640',this)">Service Sales</a>
                            </li>

                            <li><a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\ReportsController@index","frontofficeBreakfast") }}','','width=1010,height=640',this)">Breakfast</a></li>

                        </ul>
                    </li>


                    <li>
                        <a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\ReportsController@index","foPayments") }}','','width=1010,height=640',this)">
                            <img src="/images/frontdesk/card-visa-blue.svg" />
                            Payment Control
                        </a>
                    </li>

                    <li>
                        <a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\ReportsController@index","extraSales") }}','','width=1010,height=640',this)">
                            <img src="/images/frontdesk/groceries-bag.svg" />
                            Extra Sales
                        </a>
                    </li>

                    <li>
                        <a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\ReportsController@index","rooming") }}','','width=1010,height=640',this)">
                            <img src="/images/frontdesk/parking-sign.svg" />
                            Police Report
                        </a>
                    </li>

                    <li>
                        <a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\ReportsController@index","myShift") }}','','width=1010,height=640',this)">
                            <img src="/images/frontdesk/woman-suit.svg" />

                            My Shift
                        </a>
                    </li>
                    <li>
                        <a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\ReportsController@index","receptionist") }}','','width=1010,height=640',this)">
                            <img src="/images/frontdesk/support.svg" />
                            Receptionist
                        </a>
                    </li>


                    <li>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="/images/frontdesk/bed.svg" />
                            Rooms
                            <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\ReportsController@index","roomStatus") }}','','width=1010,height=640',this)">Room Status</a></li>
                            <li><a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\ReportsController@index","roomtransfers") }}','','width=1010,height=640',this)">Room Transfer</a> </li>

                        </ul>
                    </li>

                    <li>
                        <a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\ReportsController@index","banquetBooking") }}','','width=1010,height=640',this)">
                            <img src="/images/frontdesk/paste.svg" />
                            Halls Booking
                        </a>
                    </li>
                    <!--<li><a href="#">Invoices</a> </li>-->
                   <!-- <li><a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\ReportsController@index","") }}','','width=1010,height=640',this)">Payments</a></li>-->
                    <li>
                        <a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\ReportsController@index","foLogs") }}','','width=1010,height=640',this)">
                            <img src="/images/frontdesk/paste.svg" />
                            Logs
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <img src="/images/frontdesk/vacuum-cleaner.svg" />
                            Housekeeping
                            <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\ReportsController@index","housekeeping") }}','','width=1010,height=640',this)">
                                    Housekeeping
                                </a>
                            </li>
                            <li><a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\ReportsController@index","laundry") }}','','width=1010,height=640',this)">Laundry</a></li>

                        </ul>
                    </li>


                </ul>
                    </div>
            </div>

            <div class="clearfix"></div>
        </div>


    </nav>

    <div class="the_content">

        <div id="modal" class="collapse window main-modal">
            <div class="title-bar">Guest Walkin <button data-dismiss="modal" class="close"><i class="fa fa-close"></i></button> <button class="maximize"><i class="fa fa-expand"></i></button></div>
            <div class="panel-desc">
                <p class="title"></p>
                <p class="desc"></p>
                <i class="fa fa-question-circle"></i>
            </div>
            <div class="modal-body">
                <p class="text-center">Please wait ...</p>
            </div>

        </div>

        <div id="modal" class="collapse window modal-lg main-modal">
            <div class="title-bar">Guest Walkin <button data-dismiss="modal" class="close"><i class="fa fa-close"></i></button> <button class="maximize"><i class="fa fa-expand"></i></button></div>
            <div class="panel-desc">
                <p class="title"></p>
                <p class="desc"></p>
                <i class="fa fa-question-circle"></i>
            </div>
            <div class="modal-body">
                <p class="text-center">Please wait ...</p>
            </div>

        </div>


        @yield("contents")
    </div>

</body>
</html>
