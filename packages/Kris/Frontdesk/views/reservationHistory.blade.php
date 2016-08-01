@extends("Frontdesk::MasterIframe")
@section("contents")

<div class="list-filter">
    <?php $wd = \Kris\Frontdesk\Env::WD(); ?>
    <form action="" method="get" class="form-inline">
    
        <fieldset>
            <label>From Date</label>
            <input type="text" name="startdate" value="{{$wd->format("Y-m-d")}}" class="form-control datepicker" placeholder="From date" />
        </fieldset>

        <fieldset>
            <label>To Date</label>
            <input type="text" name="enddate" value="{{$wd->format("Y-m-d")}}" class="form-control datepicker" placeholder="To date" />
        </fieldset>


        <input type="submit" value="Find" class="btn btn-success btn-xs" />
    </form>
   
    <div class="clearfix"></div>
</div>

<div class="clearfix"></div>

<?php
\Session::put("back_url",\URL::previous());
?>

<a href="{{\Session::get("back_url")}}">
    Go Back
</a>
    {!! !isset($_GET['startdate']) ? "<p>Choose dates to continue</p>" : "" !!}

<div class="list-wrapper">
    <p class="list-wrapper-title">
        <span>Guest Statement({{$guest->firstname}})</span>
    </p>

    <table class="table table-bordered table-condensed data-table table-striped text-left">
        <thead>
            <tr>
                <th>#Rent ID</th>
                <th>Names</th>
                <th>Company</th>
                <th>Checkin</th>
                <th>Checkout</th>
                <th>Due</th>
                <th>Paid</th>
                <th>Balance</th>
                <th>
                    <i class="fa fa-edit"></i>
                </th>
            </tr>
        </thead>

        @if(!is_null($res))
    @foreach($res as $reservation )

        <tr>
            <td>{{$reservation->idreservation}}</td>
            <td>{{$reservation->guest->firstname}} {{$reservation->guest->lastname}}</td>
            <td>{{$reservation->company->name}}</td>
            <td>{{$reservation->checkin}}</td>
            <td>{{$reservation->checkout}}</td>
            <td>{{$reservation->due_amount}}</td>
            <td>{{$reservation->paid_amount}}</td>
            <td>{{($reservation->due_amount-$reservation->paid_amount)}}</td>
            <td>
                <i class="fa fa-edit"></i>
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