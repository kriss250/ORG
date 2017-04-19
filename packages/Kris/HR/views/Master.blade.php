<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0,target-densitydpi=device-dpi, user-scalable=no" />
    {!!HTML::style("assets/css/vendor/bootstrap.min.css")!!}
    {!!HTML::style("assets/css/vendor/font-awesome.min.css")!!}
    {!!HTML::style("assets/css/vendor/jquery-ui.min.css")!!}
    {!!HTML::style("assets/js/vendor/bsdatepicker/css/bootstrap-datepicker.min.css")!!}
    {!!HTML::style("assets/css/org_template.css")!!}
    {!!HTML::style("assets/css/hr.css")!!}
    {!!HTML::script("assets/js/vendor/jquery/jquery-1.11.2.min.js")!!}
    {!!HTML::script("assets/js/vendor/moment/moment.min.js") !!}
    {!!HTML::script("assets/js/vendor/bootstrap/bootstrap.min.js")!!}

    {!!HTML::script("assets/js/vendor/chosen/chosen.jquery.min.js")!!}
    {!!HTML::script("assets/js/vendor/jquery-ui/jquery-ui.min.js") !!}
    {!!HTML::script("assets/js/vendor/bsdatepicker/js/bootstrap-datepicker.min.js") !!}
    {!! HTML::script('assets/js/vendor/highcharts/highcharts.js') !!}
    {!!HTML::script("assets/js/hr.js")!!}
    
    <title>Human Resource Management</title>
</head>

<body class="site">

    <script>
        var baseUrl = '{{action("\Kris\HR\Controllers\PageController@home")}}';
    </script>
    <nav class="main-menu">
        <div class="menu-1">
            <ul class="grid nav nav-tabs">

                <li class="active">
                    <a data-toggle="tab" href="#pane-1">HRMS </a>
                </li>

                <li>
                    <a data-toggle="tab" href="#pane-2">Operations</a>
                </li>

                <!--<li>
                    <a data-toggle="dropdown" class="dropdown-toggle"  href="#">
                        <i class="fa fa-cogs"></i>Settings
                        <i class="fa fa-angle-down" aria-hidden="true"></i>
                    </a>

                    <ul class="dropdown-menu">
                        <li>
                            <a onclick="openWindow('newHkTask','Housekeeping Task',this,760,480)" href="#">Create User</a>
                        </li>

                        <li class="separator"></li>
                        <li>
                            <a onclick="openWindow('newLaundry','Laundry Order',this,480,310)" href="#">Users' List</a>
                        </li>
                      
                      
                    </ul>
                </li>-->

             
                <li>
                    <a data-toggle="tab" href="#pane-4">Reports</a>
                </li>
                
                <span class="notification-menu">
                    System Date : <span style="font-weight: bold !important;background: rgba(236, 240, 241, 0.86) none repeat scroll 0% 0%;padding: 2px 6px;border-radius: 3px;color: rgb(43, 92, 134);margin-right: 8px;">
                        <i class="fa fa-sun-o"></i>
                        {{date("d/m/Y")}}
                    </span>
                    
                    <li style="margin:0;padding:0;float:right;position:relative;margin-top:-3px;">
                        <a style="background:#34495e; position:relative;margin-bottom:0;margin-top:0" href="#" data-toggle="dropdown" class="btn btn-primary dropdown-toggle" type="button">
                            <i class="fa fa-user"></i> {{\Kris\Frontdesk\User::me()->username}} <span class="caret"></span>

                        </a>

                        <ul class="dropdown-menu">
                            <li><a href="{{action('\Kris\Frontdesk\Controllers\UsersController@logout')}}"><i class="fa fa-sign-out"></i> Logout</a> </li>
                        </ul>
                    </li>
                </span>




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
                                    <a class="dlg-bt" href="{{action('\Kris\HR\Controllers\PageController@open','newEmployee')}}">
                                        <img src="/images/HR/magician.svg" />
                                        New Employee
                                    </a>
                                </li>

                                <li>

                                    <a href="{{action('\Kris\HR\Controllers\PageController@open','employeesList')}}">
                                        <img src="/images/HR/file-picture.svg" />
                                        Employee List
                                    </a>
                                </li>

                              
                    
                            </ul>
                        </li>

                        <li class="menu-group">
                            <ul>
                                <li>
                                    <a href="{{action('\Kris\HR\Controllers\PageController@open','newDepartment')}}">
                                        <img src="/images/HR/organogram.svg" />
                                        New Department
                                    </a>
                                </li>

                                <li>
                                    <a href="{{action('\Kris\HR\Controllers\PageController@open','departmentList')}}">
                                        <img src="/images/HR/organogram-2.svg" />
                                        Department List
                                    </a>
                                </li>

                               
                            </ul>
                        </li>
                        <li class="menu-group">
                            <ul>
                                <li>
                                    <a href="{{action('\Kris\HR\Controllers\PageController@open','newPost')}}">
                                        <img src="/images/HR/abacus.svg" />
                                        New Post
                                    </a>
                                </li>

                                <li>
                                    <a href="{{action('\Kris\HR\Controllers\PageController@open','postList')}}">
                                        <img src="/images/HR/file-pl.svg" />
                                        Posts List
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="menu-group">
                            <ul>

                                <li>
                                    <a href="{{action('\Kris\HR\Controllers\PageController@open','newBank')}}">
                                        <img src="/images/HR/bank.svg" />
                                        New Bank
                                    </a>
                                </li>

                                <li>
                                    <a href="{{action('\Kris\HR\Controllers\PageController@open','bankList')}}">
                                        <img src="/images/frontdesk/file-attachment.svg" />
                                        Bank List
                                    </a>
                                </li>

                            </ul>

                        </li>

                        <li class="menu-group">
                            <ul>
                                <li>
                                    <a class="dlg-btn" href="{{action('\Kris\HR\Controllers\PageController@open','newAdvance')}}">
                                        <img height="32" src="/images/HR/banknote-euro.svg" />
                                        New Advance
                                    </a>
                                </li>

                                <li>
                                    <a class="dlg-btn" href="{{action('\Kris\HR\Controllers\PageController@open','advanceList')}}">
                                        <img height="32" src="/images/HR/bill.svg" />
                                        Advance List
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="menu-group">
                            <ul>
                                <li>
                                    <a href="{{action('\Kris\HR\Controllers\PageController@open','newLeave')}}">
                                        <img src="/images/HR/heart-pulse.svg" />
                                        New Leave
                                    </a>
                                </li>

                                <li>
                                    <a href="{{action('\Kris\HR\Controllers\PageController@open','leaveList')}}">
                                        <img src="/images/HR/file-heart.svg" />
                                        Leave List
                                    </a>
                                </li>

                            </ul>
                        </li>
                
                        <li class="menu-group">
                            <ul>
                                <li>
                                    <a href="{{action('\Kris\HR\Controllers\PageController@open','newCharge')}}">
                                        <img src="/images/HR/cut-prices.svg" />
                                        New Charge
                                    </a>
                                </li>

                                <li>
                                    <a href="{{action('\Kris\HR\Controllers\PageController@open','chargeList')}}">
                                        <img src="/images/HR/file-checked.svg" />
                                        Charges List
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

                      
                        <!--<li class="menu-group">
                            <ul>
                                <li>
                                    <a onclick="openWindow('addUser','Create User',this,400,500)" href="#">
                                        <img src="/images/frontdesk/add_user.svg" />
                                        Set Benefits
                                    </a>
                                </li>
                                <li>
                                    <a onclick="openWindow('userList','List of Users',this,600,400)" href="#">
                                        <img src="/images/frontdesk/user_list.svg" />
                                        Benefits Structure
                                    </a>
                                </li>

                                <li>
                                    <a onclick="openWindow('userList','List of Users',this,600,400)" href="#">
                                        <img src="/images/frontdesk/user_list.svg" />
                                        Set Leaves
                                    </a>
                                </li>
                            </ul>
                        </li>-->

                        <li style="padding:1px 15px" class="menu-group">
                            <ul>
                                <li>
                                    <a data-iframe="yes" data-toggle="modal" href="{{action('\Kris\HR\Controllers\PageController@open','newPayroll')}}">
                                        <img src="/images/frontdesk/hue.svg" />
                                        New Payroll
                                    </a>
                                </li>

                                <li>
                                    <a data-iframe="yes" href="{{action('\Kris\HR\Controllers\PageController@open','payrolls')}}">
                                        <img src="/images/frontdesk/hue.svg" />
                                        Payrolls
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="menu-group">
                            <ul>
                                <li>
                                    <a class="dlg-btn" href="{{action('\Kris\HR\Controllers\PageController@open','newTax')}}">
                                        <img height="32" src="/images/HR/scale.svg" />
                                        Create Tax
                                    </a>
                                </li>

                                <li>
                                    <a class="dlg-btn" href="{{action('\Kris\HR\Controllers\PageController@open','taxList')}}">
                                        <img height="32" src="/images/frontdesk/briefcase.svg" />
                                        Tax List
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="tab-pane" id="pane-4">
                <div style="padding-top:8px;" class="grid">
                <ul class="menu-2">


                    <li>
                        <a class="modal-btn" href='{{action("\Kris\HR\Controllers\PageController@open","reports.employee") }}'>
                            <img src="/images/frontdesk/card-visa-blue.svg" />
                            Employee Report
                        </a>
                    </li>


                    <li>
                        <a class="modal-btn" href='{{action("\Kris\HR\Controllers\PageController@open","reports.payroll") }}'>
                            <img src="/images/frontdesk/card-visa-blue.svg" />
                            Payroll Report
                        </a>
                    </li>


                    <li>
                        <a class="modal-btn" href='{{action("\Kris\HR\Controllers\PageController@open","reports.fullPayroll") }}'>
                            <img src="/images/frontdesk/card-visa-blue.svg" />
                            Payroll Report 2
                        </a>
                    </li>


                    <li>
                        <a class="modal-btn" href='{{action("\Kris\HR\Controllers\PageController@open","reports.contract") }}'>
                            <img src="/images/frontdesk/card-visa-blue.svg" />
                            Contract Report
                        </a>
                    </li>

                    <li>
                        <a class="modal-btn" href='{{action("\Kris\HR\Controllers\PageController@open","reports.employee") }}'>
                            <img src="/images/frontdesk/card-visa-blue.svg" />
                            Payroll Report
                        </a>
                    </li>


                    <li>
                        <a class="modal-btn" href='{{action("\Kris\HR\Controllers\PageController@open","reports.employee") }}'>
                            <img src="/images/frontdesk/card-visa-blue.svg" />
                            Department Report
                        </a>
                    </li>


                    <li>
                        <a class="modal-btn" href='{{action("\Kris\HR\Controllers\PageController@open","reports.employee") }}'>
                            <img src="/images/frontdesk/card-visa-blue.svg" />
                            Absence Report
                        </a>
                    </li>
                  
                    <li>
                        <a class="modal-btn" href='{{action("\Kris\HR\Controllers\PageController@open","reports.employee") }}'>
                            <img src="/images/frontdesk/card-visa-blue.svg" />
                            Adva nce Report
                        </a>
                    </li>


                </ul>
                    </div>
            </div>

            
            <div class="clearfix"></div>
        </div>


    </nav>

    <div class="the_content">
        <div class="container-fluid page">
            <div class="col-md-2 main-sidebar">
                <br />
                <p class="widget-title">Employee Finder</p>
                <div class="sidebar-widget room-av-container">

                    <form method="post">
                        <input type="hidden" name="_token" value="{{csrf_token()}}" />
                        <fieldset>
                            <label>Firstname</label>
                            <input class="datepicker" type="text" value="" placeholder="Name" />
                        </fieldset>

                        <fieldset style="position:relative">
                            <label>Lastname</label>
                            <input class="datepicker" type="text" value="" placeholder="Name" />
                        </fieldset>
                        <fieldset style="width:70px;display:table;float:left">
                            <label>Code</label>
                            <input style="width:100%" type="number" value="" placeholder="#" />
                        </fieldset>
                        <button disabled class="btn btn-success" style="margin-left:15px;margin-top:10px; display:table;float:left; padding:5px 15px;font-size:11px;font-weight:bold !important">
                            Check
                            <i class="fa fa-question-circle"></i>
                        </button>
                        <div class="clearfix"></div>
                    </form>

                </div>
                <br />
                <!--<p class="widget-title">Occupancy by type</p>
                <div style="background:rgba(255, 255, 255, 0.77);color:#6c6c6c;line-height:1.8" class="sidebar-widget">

                    <?php
            $types = \Kris\Frontdesk\Room::select( \DB::raw("type_name,alias,count(type_id)as total") )->join("room_types","type_id","=","idroom_types")->groupBy("type_id")->get();
            $types_oc = \Kris\Frontdesk\Room::select( \DB::raw("type_name,count(type_id) as total") )->join("room_types","type_id","=","idroom_types")->where("status",\Kris\Frontdesk\RoomStatus::OCCUPIED)->groupBy("type_id")->get();
            $i=0;
            $rate = 0;
                    ?>

            @foreach($types as $type)
                    <?php $roomc = isset($types_oc{$i}) ? $types_oc{$i}->total : 0; $rate = (( $roomc / $type->total)*100)-0.2;?>

                    <div class="">
                        <p style="margin:0;font-size:11px;float:left;width:30%">
                            {{$type->alias}}
                            <span style="color:#b4b4b4">{{number_format($rate)}}%</span>
                        </p>

                        <p style="float:left;width:69.2%" class="progress-bar">
                            <span style="width:{{abs($rate)}}%" data-width="{{$rate}}"></span>
                        </p>

                        <div class="clearfix"></div>
                    </div>
                    <?php $i++;?>
            @endforeach

                </div>-->
                <br />
                <!--<p class="widget-title">Expected Checkout</p>
                <div class="sidebar-widget departure-widget container-fluid"></div>-->
            </div>

            @yield("contents")
                      </div>
        </div>

</body>
</html>
