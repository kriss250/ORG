@extends("Frontdesk::MasterIframe")

@section("contents")
<div class="list-filter">
    <?php $wd = \Kris\Frontdesk\Env::WD(); ?>
    <form action="" method="post" class="form-inline">
        <input type="hidden" name="_token" value="{{csrf_token()}}" />
    
        <fieldset class="bordered">
            <label>Guest Names</label>
            <input type="text" name="todate" value="" class="form-control" placeholder="Names of the guest" />
        </fieldset>

        <fieldset class="bordered">
            <label>From Date</label>
            <input type="text" name="fromdate" value="{{$wd->format("Y-m-d")}}" class="form-control datepicker" placeholder="From date" />
        </fieldset>

        <fieldset class="bordered">
            <label>To Date</label>
            <input type="text" name="todate" value="{{$wd->format("Y-m-d")}}" class="form-control datepicker" placeholder="To date" />
        </fieldset>

        <input type="submit" value="Find" class="btn btn-success btn-xs" />
    </form>
    
    <div class="clearfix"></div>
</div>
<?php

$reservations  =  !isset($reservations)  || is_null($reservations) ? \Kris\Frontdesk\Reservation::where("status",\Kris\Frontdesk\Reservation::CHECKEDIN)->whereBetween(DB::raw("date(checkout)"),[$wd->format("Y-m-d"),$wd->format("Y-m-d")])->get() : $reservations;
?>
<table class="table table-bordered table-condensed data-table table-striped">
    <thead>
        <tr>
            <th>#ID</th>
            <th style="width:30%">Guest</th>
            <th>Room</th>
            <th>Room Type</th>
            <th>Checkin</th>
            <th>Checkout</th>
            <th>Due</th>
            <th>Paid</th>
            <th><i class="fa fa-printer"></i></th>
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
        <td class="text-left">{{$res->guest->firstname}} {{$res->guest->lastname}}</td>
        <td>{{$res->room->room_number}}</td>
        <td>{{$type->type_name}}</td>
        <td>{{$checkin}}</td>
        <td>{{$checkout}}</td>
        <td>{{$res->due_amount}}</td>
        <td>{{$res->paid_amount}}</td>
        <td>
            <i class="fa fa-eye"></i>
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
@stop