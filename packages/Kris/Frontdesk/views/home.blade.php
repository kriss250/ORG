@extends("Frontdesk::Master")

@section("contens")

<style>
    .booking_table {
        position: relative;
        padding: 20px;
        background: #fff;
    }

        .booking_table .curtain {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.89);
        }

    .table {
        font-size: 12px;
        text-align: center;
    }

    tr.houseuse {
        background-color: #fe8549;
        color: #fff;
    }

    tr.blocked {
        background-color: #2b2b2b;
        color: #fff;
    }

    .table tbody > tr > th {
        padding: 2px;
        font-size: 11px;
        text-transform: none;
        text-align: center;
        background: #e4eff2;
        font-weight: normal;
        border-color: #f1f5ff;
    }

    .table tbody > tr:nth-child(2) th {
        background: #d1e8ef;
    }

    .table tbody > tr > td {
        max-height: 23px;
        padding: 2px;
        text-align: center;
        max-width: 80px;
        overflow: hidden;
    }

    .room_status_vacant {
        color: green;
    }

    .room_status_occupied {
        color: red;
    }

    .room_status_houseuse {
        color: #fff;
    }

    .room_status_checkedout {
        color: #be48bf;
    }


    .table .tape {
        cursor: pointer;
        font-size: 11px;
        text-transform: capitalize;
        font-weight: bold;
        font-family: 'Open Sans';
        background-size: 11px 22px !important;
    }

    .tape.reserved {
        background: url("/images/tape-limit.png") right no-repeat rgb(99, 188, 224);
        color: #fff;
    }

    .tape.checkedout {
        background: url("/images/tape-limit.png") right no-repeat #be48bf;
        color: #fff;
    }

    .tape.blocked {
        background: #1e1e1e;
        color: #fff;
    }

    .tape.active {
        background: url("/images/tape-limit.png") right no-repeat rgb(99, 188, 224) !important;
        color: #fff;
    }

    .tape.houseuse {
        background: #fe8549;
        color: #fff;
    }

    .booking-counter td {
        background: #ededed;
    }



    .tape.dirty {
        background: url("/images/tape-limit.png") right no-repeat #808080;
        color: #fff;
    }

    .tape.checkedin {
        background: url("/images/tape-limit.png") right no-repeat #db6060;
        color: #fff;
    }


    .tape.continuing {
        background-image: none !important;
    }

    .booking-counter td:first-child {
        background: #eaeeff;
        font-family: Open sans;
        padding-top: 5px;
    }

    .table.table-bordered td {
        border-color: rgb(235,235,235) !important;
    }
</style>
<?php
$startdate = new DateTime($_GET['startdate']);
$days = $_GET['days'];
$html_date ="";
$html_days ="";
$html_booking_td="";
$html_date_td ="";
for($i=1;$i<=$days;$i++){
    if($i==1)
    {
        $date =  new DateTime($_GET['startdate']);
    }else {
        $date = $startdate->add(new DateInterval('P1D'));
    }
    $html_date .= "<th>{$date->format("d M")}</th>";
    $html_days .="<th>".$date->format("D")."</th>";
    $html_date_td .="<td class='room_date_".$date->format("Y-m-d")."'></td>";
    $html_booking_td .="<td class='booking_".$date->format("Y-m-d")."'>0</td>";
}
?>
<script type="text/javascript">
    $(document).ready(function () {

        $(".date-picker").change(function(){

            $("[name='filter-form']").submit();
        })

    $("[name='days']").change(function(){

            $("[name='filter-form']").submit();
        })

        var shownDays ={{$days}};

        $.ajax({
            url: '{{action("BookingViewController@getBookingData")}}?startdate={{$_GET["startdate"]}}&days={{$days}}',
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

                    $(cell).attr({
                        "colspan": spanSize,
                        "title":x.reservation_id+" : "+ x.guest,
                        "id":x.reservation_id,
                        "href": "{{action("ReservationController@show",'')}}/"+x.reservation_id,
                        "data-width":"600"
                    });

                    $(cell).addClass("modal-btn");

                    $(cell).addClass(x.days + location - 1 > shownDays ? "continuing" : "");
                    guest =  x.guest.toLowerCase().substr(0,8);
                    $(cell).html(guest);
                    $(cell).addClass("tape "+(x.status_name.replace("_","")));

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
    <div class="curtan"></div>
    
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
                Room Type
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
@stop