@extends("HR::Master")

@section("contents")


<div class="col-md-10 main-contents">
    <div class="page-title">
        <h3>Committed Payrolls</h3>
        <p style="font-size:12px;opacity:.6;margin-top:-5px;"></p>
    </div>
    <div class="row" style="padding:10px 35px;">

        <table class="blue-table table table-condensed table-bordered table-striped">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Month/Year</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>

            @foreach(\Kris\HR\Models\Payroll::all() as $py)
            <tr>
                <td>{{$py->idpayroll}}</td>
                <td>{{$py->month}}/{{$py->year}}</td>
                <td>{{$py->description}}</td>
                <td>{{$py->created_at}}</td>
               
                <td>
                    <a href="{{action("\Kris\HR\Controllers\PayrollController@remove",$py->idpayroll)}}"><i class="fa fa-trash"></i></a>
                   
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@stop