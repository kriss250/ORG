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
    <p class="report-title">Department Report</p>

    <?php $x = 1; $total = 0; ?>

   
   
    <?php $x = 1; ?>
    <table class="table table-bordered table-condensed table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Name</th>
                <th>Posts</th>
                <th>Description</th>
            </tr>
        </thead>
        <?php $x = 1; ?>
       @foreach(\Kris\HR\Models\Department::all() as $dp)
            <tr>
                <td>{{$x++}}</td>
                <td>{{$dp->iddepartments}}</td>
                <td>{{$dp->name}}</td>
                <td>{{count($dp->post)}}</td>
                <td>{{$dp->description}}</td>
            </tr>
            @endforeach
    </table>
 
  
    <br />
    @include("HR::reports.report-footer")

</div>

@stop