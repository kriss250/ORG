@extends("HR::Master")

@section("contents")


<div class="col-md-10 main-contents">
    <div class="page-title">
        <h3>Employee Charge List</h3>
        <p style="font-size:12px;opacity:.6;margin-top:-5px;">Use this form to create a new employee and set their respective departments</p>
    </div>
    <div class="row" style="padding:10px 35px;">

        <table class="blue-table table table-condensed table-bordered table-striped">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Employee</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>

            @foreach(\Kris\HR\Models\EmployeeCharge::where("deleted","0")->get() as  $ch)
            <tr>
                <td>{{$ch->idemployee_charges}}</td>
                <td>{{$ch->employee->firstname}} {{$ch->employee->lastname}}</td>
                <td>{{number_format($ch->amount)}}</td>
                <td>{{$ch->date}}</td>
                <td>{{$ch->description}}</td>
                <td>
                    <a href="{{action('\Kris\HR\Controllers\ChargeController@removeEmpCharge',$ch->idemployee_charges)}}">
                        <i class="fa fa-trash"></i>
                    </a>
                    <a href="{{action('\Kris\HR\Controllers\ChargeController@editEmpCharge',$ch->idemployee_charges)}}">
                        <i class="fa fa-pencil"></i>
                    </a>

                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@stop