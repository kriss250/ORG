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
                    <th>Code</th>
                    <th>Bank</th>
                    <th>Address</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>

            @foreach(\Kris\HR\Models\Bank::all() as $bank)
            <tr>
                <td>{{$bank->idbanks}}</td>
                <td>{{$bank->bank_name}}</td>
                <td>{{$bank->address}}</td>
                <td>{{$bank->description}}</td>
                <td>
                    <a href=""><i class="fa fa-trash"></i></a>
                    <a href="{{action("\Kris\HR\Controllers\BankController@edit",$bank->idbanks)}}">
                        <i class="fa fa-pencil"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@stop