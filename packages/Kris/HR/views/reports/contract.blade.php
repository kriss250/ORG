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
    <p class="report-title">Contract Report</p>

    <?php $x = 1; $total = 0; ?>

   
   
    <?php $x = 1; ?>
    <table class="table table-bordered table-condensed table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Department</th>
                <th>Post</th>
                <th>Start Date</th>
                <th>End Date</th>
            </tr>
        </thead>
       @foreach(\Kris\HR\Models\EmployeeContract::whereNull("termination_date")->get() as $con)
        <?php $con->employee->load("department"); $con->employee->load("post");   ?>
        <tr {!!\Carbon\Carbon::now()->lt((new \Carbon\Carbon($con->end_date))) ? 'style="text-decoration:line-through"' :""!!}>
            <td>{{$x}}</td>
            <td>{{$con->employee->firstname}} {{$con->employee->lastname}}</td>
            <td>{{$con->employee->department->name}}</td>
            <td>{{$con->employee->post->name}}</td>
            <td>{{ $con->start_date == null ? "" : (new \Carbon\Carbon($con->start_date))->format("d/m/Y")}}</td>
            <td>{{ $con->end_date == null ? "-" : (new \Carbon\Carbon($con->end_date))->format("d/m/Y")}}</td>
        </tr>

        <?php $x++; ?>
       @endforeach
    </table>
 
  
    <br />
    @include("HR::reports.report-footer")

</div>

@stop