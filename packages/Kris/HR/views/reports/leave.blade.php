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
    <p class="report-title">Leave Report</p>

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
                <th>Days</th>
                <th>Date</th>
            </tr>
        </thead>
       @foreach(\Kris\HR\Models\EmployeeLeave::all() as $lv)
        <?php $lv->employee->load("department"); $lv->employee->load("post");   ?>
        <tr>
            <td>{{$x}}</td>
            <td>{{$lv->employee->firstname}} {{$lv->employee->lastname}}</td>
            <td>{{$lv->employee->department->name}}</td>
            <td>{{$lv->employee->post->name}}</td>
            <td>{{ $lv->start_date == null ? "" : (new \Carbon\Carbon($lv->start_date))->format("d/m/Y")}}</td>
            <td>{{ $lv->end_date == null ? "-" : (new \Carbon\Carbon($lv->end_date))->format("d/m/Y")}}</td>
            <td>{{(new \Carbon\Carbon($lv->end_date))->diff((new \Carbon\Carbon($lv->start_date)))->d}}</td>
            <td>{{(new \Carbon\Carbon($lv->created_at))->format("d/m/Y")}}</td>
        </tr>

        <?php $x++; ?>
       @endforeach
    </table>
 
  
    <br />
    @include("HR::reports.report-footer")

</div>

@stop