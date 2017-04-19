@extends("HR::Master")

@section("contents")


<div class="col-md-10 main-contents">
    <div class="page-title">
        <h3>List of Advances</h3>
        <p style="font-size:12px;opacity:.6;margin-top:-5px;">Use this form to create a new employee and set their respective departments</p>
    </div>
    <div class="row" style="padding:10px 35px;">

        <table class="blue-table table table-condensed table-bordered table-striped">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Employee</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>

            @foreach(\Kris\HR\Models\Advance::where("deleted","0")->get() as $ad)
            <tr>
                <td>{{$ad->employee->idemployees}}</td>
                <td>{{$ad->employee->firstname}} {{$ad->employee->lastname}}</td>
                <td>{{$ad->amount}}</td>
                <td>{{$ad->description}}</td>
                <td>{{$ad->date}}</td>
                <td><a href="{{action("\Kris\HR\Controllers\AdvanceController@remove",$ad->idadvances)}}"><i class="fa fa-trash"></i></a></td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@stop