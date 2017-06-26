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
<div style="padding:0" class="inline-fieldsets list-wrapper">
    <p class="list-wrapper-title">Extra Sales</p>
    <table class="table table-condensed table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Guest</th>
                <th>Service</th>
                <th>Paid</th>
                <th>Credit</th>
                <th>Pay. Method</th>
                <th>User</th>
                <th>Date</th>
                <th><i class="fa fa-trash"></i></th>
            </tr>
        </thead>

        @foreach($data as $sale)
        <tr>
            <td>{{$sale->idmisc_sales}}</td>
            <td class="text-left">{{$sale->guest}}</td>
            <td>{{$sale->service}}</td>
            <td>{{$sale->is_credit == "0" ? $sale->amount : ""}}</td>
            <td>{{$sale->is_credit == "1" ? $sale->amount : ""}}</td>
            <td>{{            $sale->method_name}}</td>
            <td>{{$sale->username}}</td>
            <td>{{            $sale->date}}</td>
            <td>
                <ul class="list-inline">

                    <li class="col-xs-5 pull-left">
                        <a class="btn btn-default btn-xs" title="Print" data-toggle="tooltip" onclick="openWindow('printExtraSalesReceipt/{{$sale->idmisc_sales}}',this,'Print Receipt',970,580);" href="#">
                            <i class="fa fa-print"></i>
                        </a>
                    </li>

                    <!--<li class="col-xs-5 pull-right">
                        <a class="btn btn-xs btn-danger" href="#">
                            <i class="fa fa-trash"></i>
                        </a>
                    </li>-->

                    
                </ul>

                
            </td>
        </tr>
        @endforeach
    </table>
</div>
@if(isset($data) && count($data)>0)

@endif
@stop