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

<a href="#" style="margin-left:15px;font-size:12px" onclick="history.back()">
    << Go Back
</a>
    {!! !isset($_GET['startdate']) ? "<p class='text-center'>Choose dates to continue</p>" : "" !!}

<div class="list-wrapper">
    <p class="list-wrapper-title">
        <span>Statement({{isset($guest) ?  $guest->firstname : $company->name}})</span>
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
            <td>{{(new \Carbon\Carbon($reservation->checkin))->format("d/m/Y")}}</td>
            <td>{{(new \Carbon\Carbon($reservation->checkout))->format("d/m/Y")}}</td>
            <td>{{$reservation->due_amount}}</td>
            <td>{{$reservation->paid_amount}}</td>
            <td>{{($reservation->due_amount-$reservation->paid_amount)}}</td>
            <td>
                <span href="#" data-placement="left" class="pop-toggle btn-xs btn btn-default" aria-haspopup="true" aria-expanded="false"><i class="fa fa-eye"></i>
                    <ul class="dropdown-menu">
                        
                        <li><a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\OperationsController@_print",'bill')}}?id={{$reservation->idreservation}}&type=standard','','width=920,height=620',this)" href="#">Standard Bill</a></li>
                        <li><a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\OperationsController@_print",'bill')}}?id={{$reservation->idreservation}}&type=payments','','width=920,height=620',this)" href="#">With Payments</a></li>
                        <li><a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\OperationsController@_print",'bill')}}?id={{$reservation->idreservation}}&type=accomodation','','width=920,height=620',this)" href="#">Accomodation</a></li>
                        <li><a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\OperationsController@_print",'bill')}}?id={{$reservation->idreservation}}&type=services','','width=920,height=620',this)" href="#">Services</a></li>
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