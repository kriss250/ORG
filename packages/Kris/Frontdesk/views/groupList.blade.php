@extends("Frontdesk::MasterIframe")

@section("contents")

<div class="panel-desc">
    <p class="title">Reservations</p>
    <p class="desc">List of reservations , grouped by date and status</p>
</div>
<div class="list-filter">
    <?php $wd = \Kris\Frontdesk\Env::WD(); ?>
    <form action="{{action('\Kris\Frontdesk\Controllers\OperationsController@forms',"groupreservationlist")}}" method="post" class="form-inline">
        <input type="hidden" name="_token" value="{{csrf_token()}}" />

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

<div class="list-wrapper">
     <p class="list-wrapper-title">
        <span>Group Reservations</span>
    </p>

    <table class="table table-bordered table-condensed data-table table-striped">
        <thead>
            <tr>
                <th>Group Name</th>
                <th>Checkin</th>
                <th>Checkout</th>
                <th>N<sup>0</sup> Rooms</th>
                <th>Open</th>
            </tr>
        </thead>

        @if(!empty($groups))
    @foreach($groups as $group )
        <?php
         $checkin = (new \Carbon\Carbon($group->arrival))->format("d/m/Y");
         $checkout = (new \Carbon\Carbon($group->departure))->format("d/m/Y");
        ?>
        <tr>
            <td>{{$group->group_name}}</td>
            <td>{{$checkin}}</td>
            <td>{{$checkout}}</td>
            <td>{{$group->reservation->count()}}</td>
            <td>
                <button class="btn btn-xs" onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\OperationsController@frame","groupViewer")}}?id={{$group->groupid}}','Reservation','width=850,height=590,resizable=no',this)">
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