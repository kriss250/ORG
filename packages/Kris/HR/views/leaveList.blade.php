@extends("HR::Master")

@section("contents")


<div class="col-md-10 main-contents">
    <div class="page-title">
        <h3>List of Leaves</h3>
        <p style="font-size:12px;opacity:.6;margin-top:-5px;">Use this form to create a new employee and set their respective departments</p>
    </div>
    <div class="row" style="padding:10px 35px;">

        <table class="blue-table table table-condensed table-bordered table-striped">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Employee</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Description</th>
                    <th>Creation Date</th>
                    <th>Action</th>
                </tr>
            </thead>

            @foreach(\Kris\HR\Models\EmployeeLeave::all() as $lv)
            <tr>
                <td>{{$lv->idemployee_leaves}}</td>
                <td>{{$lv->employee->firstname}} {{$lv->employee->lastname}}</td>
                <td>{{$lv->start_date}}</td>
                <td>{{$lv->end_date}}</td>
                <td>{{$lv->description}}</td>
                <td>{{$lv->created_at}}</td>
                <td><a href="{{action("\Kris\HR\Controllers\LeaveController@remove",$lv->idemployee_leaves)}}"><i class="fa fa-trash"></i></a></td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@stop