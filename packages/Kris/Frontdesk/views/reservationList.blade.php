@extends("Frontdesk::MasterIframe")

@section("contents")

<div class="panel-desc">
    <p class="title">Reservations</p>
    <p class="desc">List of reservations , grouped by date and status</p>
</div>
<div class="list-filter">
    <?php $wd = \Kris\Frontdesk\Env::WD(); ?>
    <form action="{{action('\Kris\Frontdesk\Controllers\ReservationsController@listReservations')}}" method="post" class="form-inline">
        <input type="hidden" name="_token" value="{{csrf_token()}}" />
        <fieldset class="bordered">
            <label>Reservation status</label>
            <span>Active </span><input name="status" value="{{\Kris\Frontdesk\Reservation::ACTIVE}}" checked type="radio" /> <span>Cancelled</span> <input {{\Kris\Frontdesk\Reservation::CANCELLED}} name="status" type="radio" /> <span>No show</span> <input value="{{\Kris\Frontdesk\Reservation::NOSHOW}}" name="status" type="radio" />
        </fieldset>

        <fieldset class="bordered">
            <label>From Date</label>
            <input type="text" name="fromdate" value="{{$wd->format("Y-m-d")}}" class="form-control datepicker" placeholder="From date" />
        </fieldset>

        <fieldset class="bordered">
            <label>To Date</label>
            <input type="text" name="todate" value="{{$wd->addDays(6)->format("Y-m-d")}}" class="form-control datepicker" placeholder="To date" />
        </fieldset>

        <input type="submit" value="Find" class="btn btn-success btn-xs" />
    </form>
    
    <div class="clearfix"></div>
</div>
<?php
$reservations  = !isset($reservations) ? \Kris\Frontdesk\Reservation::where("status",\Kris\Frontdesk\Reservation::ACTIVE)->whereBetween("checkin",[\Kris\Frontdesk\Env::WD()->format("Y-m-d"),\Kris\Frontdesk\Env::WD()->addDays(6)->format("Y-m-d")])->get() : $reservations;

?>
<div class="list-wrapper">
     <p class="list-wrapper-title">
        <span>Room Reservations</span>
    </p>

    <table class="table table-bordered table-condensed data-table table-striped">
        <thead>
            <tr>
                <th>#ID</th>
                <th style="width:30%">Guest</th>
                <th>Room</th>
                <th>Room Type</th>
                <th>Rate</th>
                <th>Checkin</th>
                <th>Checkout</th>
                <th>Open</th>
            </tr>
        </thead>

        @if(!empty($reservations))
    @foreach($reservations as $res )
        <?php
         $checkin = (new \Carbon\Carbon($res->checkin))->format("d/m/Y");
         $checkout = (new \Carbon\Carbon($res->checkout))->format("d/m/Y");
         $type = \Kris\Frontdesk\RoomType::find($res->room->type_id);
        ?>
        <tr>
            <td>{{$res->idreservation}}</td>
            <td class="text-left">{{$res->guest != null ? $res->guest->firstname : ""}} {{$res->guest != null ? $res->guest->lastname : ""}}</td>
            <td>{{$res->room->room_number}}</td>
            <td>{{$type->type_name}}</td>
            <td>{{number_format($res->night_rate)}}</td>
            <td>{{$checkin}}</td>
            <td>{{$checkout}}</td>
            <td>
                <button class="btn btn-xs" onclick="openRoom({{$res->idreservation}},this)">
                    <i class="fa fa-eye"></i>
                </button>
            </td>
        </tr>
        @endforeach
    @else
        <tr>
            <td colspan="9">
                No data
            </td>
        </tr>
        @endif
    </table>

</div>
@stop