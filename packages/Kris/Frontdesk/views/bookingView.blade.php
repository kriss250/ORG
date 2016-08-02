@extends("Frontdesk::Master")

@section("contents")

<style>
    .occupied {
        color: #e74c3c;
    }


    .reserved {
        color: #6daed9;
    }

    .checked.out {
        color: #9b59b6;
    }

    .house.use {
        color: #f39c12;
    }

    .vacant {
        color: #2ecc71;
    }

    .blocked {
        color: #000 !important;
    }
</style>
<div class="container-fluid page">
    <div class="col-md-2">

        <div  class="booking-view-sidebar-label">
            <p><i class="fa fa-info-circle"></i> Showing</p>
            Starting Date : {{$_GET['startdate']}}
        </div>
        <br />
        <div class="bookingview-switch">
            <form method="get" action="{{\Request::URL()}}">
                <fieldset style="width:98px;float:left">
                    <label>Start Date</label>
                    <input name="startdate" type="text" value="{{$_GET['startdate']}}" style="height:28px;border-radius:0;font-size:12px" class="form-control datepicker" />
                </fieldset>

                <fieldset style="width:60px;display:table;float:left;margin-left:8px;">
                    <label>Days</label>
                    <input step="7" name="days" style="margin-top:10px;" type="number" value="{{$_GET['days']}}" placeholder="#" />
                </fieldset>

                <button style="width:20px;margin-top:15px;border-radius:50%; height:20px;margin-left:3px" type="submit" class="btn btn-xs btn-default">
                    <i class="fa fa-angle-right"></i>
                </button>
            </form>
        </div>

        <div class="clearfix"></div>
        <p style="font-size:12px">Room Info</p>
        <?php
        
        $room_status = \Kris\Frontdesk\RoomStatus::select(\DB::raw("room_number,idrooms,status_name,count(status) as qty"))->leftJoin("rooms","status_code","=","status")->groupBy("status_code")->get();
        $_qty = 0;
        ?>

        <table class="room-status-table">
            <thead>
                <tr>
                    <th colspan="2">ROOM STATUS</th>
                </tr>
            </thead>

            @foreach($room_status as $status)
            <tr class="{{strtolower($status->status_name)}}">
                <td>{{$status->status_name}}</td>
                <td>{{$status->qty}}</td>
                <?php $_qty += $status->qty; ?>
            </tr>
            @endforeach
            <tfoot style="border-bottom:none">
                <tr>
                    <th colspan="2">Number of rooms : {{$_qty}}</th>
                </tr>
            </tfoot>
        </table>
        <p class="widget-title">Room Availability</p>
        <div class="sidebar-widget room-av-container">
          
            <form>
               
                <fieldset>
                    <label>Checkin</label>
                    <input type="text" value="" placeholder="YYYY-MM-DD" />
                </fieldset>

                <fieldset>
                    <label>Checkout</label>
                    <input type="text" value="" placeholder="YYYY-MM-DD" />
                </fieldset>
                <fieldset style="width:60px;display:table;float:left">
                    <label>Quantity</label>
                    <input style="width:100%" type="number" value="" placeholder="#" />
                </fieldset>
                <button class="btn btn-success" style="margin-left:15px;margin-top:10px; display:table;float:left; padding:5px 15px;font-size:11px;font-weight:bold !important">Check 
<i class="fa fa-question-circle"></i></button>
                <div class="clearfix"></div>
            </form>
        </div>
        <br />
        <p class="widget-title">Expected Checkout</p>
        <div class="sidebar-widget departure-widget container-fluid">

           
        </div>
    </div>

    <div class="col-md-10 main-contents">

        <?php
        $startdate = new \Carbon\Carbon($_GET['startdate']);
        $days = $_GET['days'];
        $html_date ="";
        $html_days ="";
        $html_booking_td="";
        $html_date_td ="";


        for($i=1;$i<=$days;$i++){
            if($i==1)
            {
                $date =  new \Carbon\Carbon($_GET['startdate']);
            }else {
                $date = $startdate->addDays(1);
            }
            $html_date .= "<th>{$date->format("d M")}</th>";
            $html_days .="<th>".$date->format("D")."</th>";
            $html_date_td .="<td class='room_date_".$date->format("Y-m-d")."'></td>";
            $html_booking_td .="<td class='booking_".$date->format("Y-m-d")."'>0</td>";
        }
        ?>

        <script type="text/javascript">
    $(document).ready(function () {



        var shownDays ={{$days}};

        $.ajax({
            url: '{{action("\Kris\Frontdesk\Controllers\OperationsController@getBookingData")}}?startdate={{$_GET["startdate"]}}&days={{$days}}',
            type: "get",
            success: function (data) {
                data = JSON.parse(data);

                $.each(data,function (i, x) {
                    var the_date = '{{$_GET["startdate"]}}';
                    var prv_days = 0;
                    x.days++;
                    var cell = $(".room_" + x.room_id + " .room_date_" + x.checkin);
                    var location = $(cell).index();
                    var spanSize = 1;

                    spanSize = (x.days + location -1 > shownDays ? shownDays-location+1 : x.days);

                    $(cell).attr("colspan",spanSize );

                    $(cell).addClass(x.days + location - 1 > shownDays ? "continuing" : "");
                    guest =  x.guest.toLowerCase().substr(0,8);
                    $(cell).html(guest);
                    $(cell).addClass("tape "+(x.status_name.replace("_","")));
                    var url = '{{action("\Kris\Frontdesk\Controllers\OperationsController@roomView",'_id_')}}';
                    url = url.replace('_id_',x.reservation_id);

                    if(typeof x.reservation_id != "undefined"){
                        $(cell).attr("title","Room :"+x.room_number).attr("onclick","window.openDialog('"+url+"','room','width=800,height=590,resizable=no',this)" );
                    }

                    if(location > -1)
                    {

                        //remove cells after it (number of days)

                        for (i =1; i < spanSize; i++)
                        {
                            if(typeof $(".room_" + x.room_id + " td")[location+i] !== "undefined")
                            {
                                var item = $(".room_" + x.room_id + " td")[location + i];
                                $(item).hide();
                            }
                        }
                    }
                });

                $(".booking_table .curtain").fadeOut(600);
                $(".booking_table img").hide();
            }
        })
    });
        </script>

        <div class="booking_table">
            <div class="curtain"></div>
            <div class="report-filter">
                <form name="filter-form" class="row form-inline">
                    <!--<div class="col-md-6">
              
                <label>Start Date</label>
                <input name="startdate" style="width:90px;" class="form-control date-picker" value="{{$_GET['startdate']}}" />
                Period <select class="form-control" name="days">
                    <option {{$_GET['days']== 7 ? " selected " : ""}} value="7">1 Week</option>
                <option {{$_GET['days']== 14 ? " selected " : ""}} value="14">2 Weeks</option>
                <option {{$_GET['days']== 21 ? " selected " : ""}} value="21">3 Weeks</option>
                <option {{$_GET['days']== 28 ? " selected " : ""}} value="28">4 Weeks</option>
                </select>
            </div>-->

                    <!--<div class="col-md-6">
                <p style="margin-bottom:-10px;margin-top:10px;">Color Map</p>
                <br />
                <span style="width:10px;height:10px;background:#db4a4a;display:inline-block;"></span> Occupied
                <span style="width:10px;height:10px;background: rgb(109, 202, 239);display:inline-block;"></span> Reserved
                <span style="width:10px;height:10px;background:#be48bf;display:inline-block;"></span> Checkedout
                <span style="width:10px;height:10px;background:#ff7d3b;display:inline-block;"></span> House Use
                <span style="width:10px;height:10px;background:#2b2b2b;display:inline-block;"></span> Blocked
            </div>-->
                </form>
            </div>
            <img style="position:absolute;top:100px;left:40%" src="/assets/images/small-loader.gif" />
            <table style="background-color:#fff" class="table table-bordered">

                <tr>
                    <th class="text-center" colspan="2">
                        Date
                    </th>

                    {!!$html_date!!}
                </tr>

                <tr>
                    <th>
                        R.Type
                    </th>
                    <th>
                        Room
                    </th>
                    {!!$html_days!!}
                </tr>


                @foreach($types as $key=>$type)
                <tr class="booking-counter">
                    <td rowspan="{{(count($type)+1)}}" colspan="1">{{$key}}</td>
                    <td>Booking</td>

                    {!! $html_booking_td !!}
                </tr>


                @foreach($type as $room)
                <tr class="{{strtolower( str_replace(' ','',$room->status_name))}} room_{{$room->id}}">
                    <td class="room_status_{{strtolower( str_replace(' ','',$room->status_name)) }}">{{$room->room_number}}</td>
                    {!!$html_date_td!!}
                </tr>
                @endforeach


        @endforeach
            </table>
        </div>
    </div>
</div>
@stop