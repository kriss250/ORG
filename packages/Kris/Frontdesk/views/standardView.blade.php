@extends("Frontdesk::Master")

@section("contents")

<?php
$ExpectedCheckout = [];
?>
<div class="container-fluid page">
    <div class="col-md-2">
        <br />
        <p class="widget-title">Room Availability</p>
        <div class="sidebar-widget room-av-container">

            <form method="post">
                <fieldset>
                    <label>Checkin</label>
                    <input class="datepicker" type="text" value="" placeholder="YYYY-MM-DD" />
                </fieldset>

                <fieldset>
                    <label>Checkout</label>
                    <input class="datepicker" type="text" value="" placeholder="YYYY-MM-DD" />
                </fieldset>
                <fieldset style="width:70px;display:table;float:left">
                    <label>Quantity</label>
                    <input style="width:100%" type="number" value="" placeholder="#" />
                </fieldset>
                <button class="btn btn-success" style="margin-left:15px;margin-top:10px; display:table;float:left; padding:5px 15px;font-size:11px;font-weight:bold !important">
                    Check
                    <i class="fa fa-question-circle"></i>
                </button>
                <div class="clearfix"></div>
            </form>

        </div>
        <br />
        <p class="widget-title">Occupancy by type</p>
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

        </div>
        <br />
        <p class="widget-title">Expected Checkout</p>
        <div class="sidebar-widget departure-widget container-fluid">


        </div>
    </div>

    <div class="col-md-10 main-contents">
        <div style="padding-top:8px;">
            <div class="status-wrapper">
                <h6 style="font-weight:bold;color:#6b6b6b;font-size:11px;">SUMMARY</h6>
                <ul class="status-list">
                    <?php
                    $statuses = (new \Kris\Frontdesk\RoomStatus)->getStatusCount();
                    $room_status_count = [];
                    foreach($statuses as $status)
                    {
                        $room_status_count[$status->status_code]   = $status->cnt;
                    }

                    ?>
                    <li class="vc_status">
                        <p>VACANT</p>
                        <span>{{$room_status_count[\Kris\Frontdesk\RoomStatus::VACANT]}}</span> Rooms
                        <i class="fa fa-star"></i>
                    </li>

                    <li class="oc_status">
                        <p>OCCUPIED</p>
                        <span>{{$room_status_count[\Kris\Frontdesk\RoomStatus::OCCUPIED]}}</span>Rooms
                        <i class="fa fa-male"></i>
                    </li>

                    <li class="rs_status">
                        <p>RESERVED</p>
                        <span>{{$room_status_count[\Kris\Frontdesk\RoomStatus::RESERVED]}}</span>Rooms
                        <i class="fa fa-calendar"></i>
                    </li>

                    <li class="co_status">
                        <p>CHECKED OUT</p>
                        <span>{{$room_status_count[\Kris\Frontdesk\RoomStatus::CHECKEDOUT]}}</span>Rooms
                        <i class="fa fa-luggage"></i>
                    </li>
                    <li class="dt_status">
                        <p>DIRTY</p>
                        <span>{{$room_status_count[\Kris\Frontdesk\RoomStatus::DIRTY]}}</span>Rooms
                        <i class="fa fa-star"></i>
                    </li>

                    <li class="hu_status">
                        <p>HOUSE USE</p>
                        <span>{{$room_status_count[\Kris\Frontdesk\RoomStatus::HOUSEUSE]}}</span>Rooms
                        <i class="fa fa-home"></i>
                    </li>

                    <li class="bl_status">
                        <p>BLOCKED</p>
                        <span>{{$room_status_count[\Kris\Frontdesk\RoomStatus::BLOCKED]}}</span>Rooms
                        <i class="fa fa-ban"></i>
                    </li>
                </ul>
            </div>
            <div class="clearfix"></div>

            <table class="rooms-table table table-condensed table-bordered table-striped">
                <thead>
                    <tr>
                        <th>
                            Room
                        </th>

                        <th>
                            Room Type
                        </th>

                        <th>
                            Floor
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Guest Names
                        </th>

                        <th>
                            Company
                        </th>

                        <th>
                            Checkin
                        </th>

                        <th>
                            Checkout
                        </th>

                    </tr>
                </thead>

                @foreach($data as $room)
                <tr onclick="openRoom({{$room->idreservation}},this)" data-iframe="yes" data-desc="" class="{{strtolower($room->status_name)}} dlg-bn" title="Room : {{$room->room_number}} Status : {{$room->status_name}}" data-toggle="modal" data-url="{{action("\Kris\Frontdesk\Controllers\OperationsController@roomView",$room->idreservation)}}" data-target=".moda-lg" data-id="{{$room->idreservation}}">
                    <td>
                        {{$room->room_number}}
                    </td>

                    <td>
                        {{$room->type_name}}
                    </td>

                    <td>
                        {{$room->floor_name}}
                    </td>

                    <td>
                        {{$room->status_name}}
                    </td>

                    <td style="width:22%">
                        {{$room->Guest}}
                    </td>

                    <td>
                        {{$room->name}}
                    </td>

                    <td>
                        {{strlen($room->checkin) > 1 ?  \Kris\Frontdesk\Env::formatDT($room->checkin) :""}}
                    </td>

                    <td>
                        {{strlen($room->checkout) > 1 ? \Kris\Frontdesk\Env::formatDT($room->checkout) :""}}
                    </td>
                    <?php
                            $cb = new Carbon\Carbon($room->checkout);
                            if($cb->eq(new Carbon\Carbon(\Kris\Frontdesk\Env::WD())))
                            {
                                $ExpectedCheckout[] = $room;
                            }
                    ?>
                </tr>

                @endforeach

                <?php
if(!empty($ExpectedCheckout))
{
    $script = "";
    foreach($ExpectedCheckout as $room){
        $script .= "<span><b>{$room->room_number}</b>".substr($room->Guest,0,10)."</span>";
    }
    echo "<script> $(document).ready(function(){ $('.departure-widget').append('{$script}'); }) </script>";
}else {
    echo "<script> $(document).ready(function(){ $('.departure-widget').html('<i style=\"font-size:11px;opacity:.5\">There is no expected checkout</i>'); }) </script>";
}
                ?>
            </table>
        </div>
    </div>
</div>
@stop