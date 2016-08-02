@extends("Frontdesk::MasterIframe")

@section("contents")

<div class="panel-desc">
    <p class="title">Room Bills</p>
    <p class="desc"></p>
</div>

<div class="list-filter">
    <?php $wd = \Kris\Frontdesk\Env::WD(); ?>
    <form action="" method="post" class="form-inline">
        <input type="hidden" name="_token" value="{{csrf_token()}}" />
    
        <fieldset class="bordered">
            <label>Guest Names </label>
            <input type="text" name="guest" value="" class="form-control" placeholder="Names of the guest" />
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

<div class="list-wrapper">
    <p class="list-wrapper-title">
        <span>Customer Bills</span>
    </p>
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
            <th><i class="fa fa-eye"></i></th>
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
             <span href="#" data-placement="left" class="pop-toggle btn-xs btn btn-default" aria-haspopup="true" aria-expanded="false"><i class="fa fa-eye"></i>
                    <ul class="dropdown-menu">
                        
                        <li><a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\OperationsController@_print",'bill')}}?id={{$res->idreservation}}&type=standard','','width=920,height=620',this)" href="#">Standard Bill</a></li>
                        <li><a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\OperationsController@_print",'bill')}}?id={{$res->idreservation}}&type=payments','','width=920,height=620',this)" href="#">With Payments</a></li>
                        <li><a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\OperationsController@_print",'bill')}}?id={{$res->idreservation}}&type=accomodation','','width=920,height=620',this)" href="#">Accomodation</a></li>
                        <li><a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\OperationsController@_print",'bill')}}?id={{$res->idreservation}}&type=services','','width=920,height=620',this)" href="#">Services</a></li>
                    </ul>
                </span>
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