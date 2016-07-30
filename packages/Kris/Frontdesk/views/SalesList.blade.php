@extends("Frontdesk::MasterIframe")

@section("contents")

<div class="list-filter">
    <?php $wd = \Kris\Frontdesk\Env::WD(); ?>
    <form action="" method="get" class="form-inline">
  
        <fieldset class="bordered">
            <label>From Date</label>
            <input type="text" name="startdate" value="{{$wd->format("Y-m-d")}}" class="form-control datepicker" placeholder="From date" />
        </fieldset>

        <fieldset class="bordered">
            <label>To Date</label>
            <input type="text" name="enddate" value="{{$wd->addDays(6)->format("Y-m-d")}}" class="form-control datepicker" placeholder="To date" />
        </fieldset>

        <input type="submit" value="Find" class="btn btn-success btn-xs" />
    </form>

    <div class="clearfix"></div>
</div>

<table class="table table-condensed table-bordered">
    <thead>
        <tr>
            <th>Guest</th>
            <th>Service</th>
            <th>Receipt</th>
            <th>Paid</th>
            <th>Credit</th>
            <th>Pay. Method</th>
            <th>User</th>
            <th>Date</th>
        </tr>
    </thead>

    @foreach($data as $sale)
    <tr>
        <td class="text-left">{{$sale->guest}}</td>
        <td>{{$sale->service}}</td>
        <td>{{$sale->receipt}}</td>
        <td>{{$sale->is_credit == "0" ? $sale->amount : ""}}</td>
        <td>{{$sale->is_credit == "1" ? $sale->amount : ""}}</td>
        <td>{{$sale->method_name}}</td>
        <td>{{$sale->username}}</td>
        <td>{{$sale->date}}</td>
    </tr>
    @endforeach
</table>

@if(isset($data) && count($data)>0)

@endif
@stop