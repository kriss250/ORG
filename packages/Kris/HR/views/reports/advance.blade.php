@extends("Frontdesk::MasterIframe")

@section("contents")
<div class="report-filter">
    <form action="" method="get">
        <div style="width:100%;max-width:980px;margin:auto" class="row">
            <div class="col-xs-5">
                <h4>HR Reports</h4>
            </div>

            <div class="col-xs-7 container-fluid text-right">

                <div class="col-xs-4">
                   
                    <input style="max-width:100%" name="startdate" type="text" value="{{\Kris\Frontdesk\Env::WD()->format("Y-m-d")}}" class="datepicker form-control" />-
                </div>
                <div class="col-xs-4">
                    <input name="enddate" type="text" value="{{\Kris\Frontdesk\Env::WD()->format("Y-m-d")}}" class="datepicker form-control" />
                </div>

                <div class="col-xs-3">  
                    <input type="submit" class="btn btn-success btn-sm" value="Go" />
                    <button type="button" onclick="window.print()" class="btn btn-default report-print-btn">Print</button>
                </div>
            </div>

        </div>
    </form>
</div>
<div class="print-document"> 
    @include("HR::reports.report-print-header")
    <p class="report-title">Advance Report</p>

    <?php $x = 1; $total = 0; ?>
   
    <?php $x = 1; ?>
    <table class="table table-bordered table-condensed table-striped">
         <thead>
                <tr>
                    <th>Code</th>
                    <th>Employee</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Date</th>
                 
                </tr>
            </thead>

            @foreach(\Kris\HR\Models\Advance::where("deleted","0")->get() as $ad)
            <tr>
                <td>{{$ad->employee->idemployees}}</td>
                <td>{{$ad->employee->firstname}} {{$ad->employee->lastname}}</td>
                <td>{{$ad->amount}}</td>
                <td>{{$ad->description}}</td>
                <td>{{$ad->date}}</td>
            </tr>
            <?php $total += $ad->amount; ?>
            @endforeach

<tfoot>
<tr>
    <th colspan="2">TOTAL</th>
    <th>{{number_format($total)}}</th>
</tr>
</tfoot>

    </table>
 
  
    <br />
    @include("HR::reports.report-footer")

</div>

@stop