@extends("HR::Master")

@section("contents")


<div class="col-md-10 main-contents">
    <div class="page-title">
        <h3>List of banks</h3>
        <p style="font-size:12px;opacity:.6;margin-top:-5px;">Use this form to create a new employee and set their respective departments</p>
    </div>
    <div class="row" style="padding:10px 35px;">

        <table class="blue-table table table-condensed table-bordered table-striped">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Days</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>

            @foreach(\Kris\HR\Models\Absence::all() as $ab)
            <tr>
                <td>{{$ab->employee->firstname}} {{$ab->employee->lastname}}</td>
                <td>{{$ab->from_date}}</td>
                <td>{{$ab->to_date}}</td>
                <td>{{$ab->days}}</td>
                <td>{{$ab->description}}</td>
                <td>
                    <a href=""><i class="fa fa-trash"></i></a>
                    <a href="{{action("\Kris\HR\Controllers\AbsennceController@edit",$ab->idabsence)}}">
                        <i class="fa fa-pencil"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@stop