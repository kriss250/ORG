@extends("HR::Master")

@section("contents")


<div class="col-md-10 main-contents">
    <div class="page-title">
        <h3>List of registered taxes</h3>
        <p style="font-size:12px;opacity:.6;margin-top:-5px;">Use this form to create a new employee and set their respective departments</p>
    </div>
    <div class="row" style="padding:10px 35px;">

        <table class="blue-table table table-condensed table-bordered table-striped">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Tax Name</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Creation Date</th>
                    <th>Action</th>
                </tr>
            </thead>

            @foreach(\Kris\HR\Models\Tax::all() as  $tax)
            <tr>
                <td>{{$tax->idtaxes}}</td>
                <td>{{$tax->tax_name}}</td>
                <td><?php
                    switch($tax->type_id)
                    {
                        case \Kris\HR\Models\Type::FIXED:
                            print "fixed";
                            break;
                        case \Kris\HR\Models\Type::PERCENT:
                            print "%";
                            break;
                    }
                    ?>
                </td>
                <td>{{$tax->value}}</td>
                <td>{{$tax->created_at}}</td>
                    <td>
                    <a href=""><i class="fa fa-trash"></i></a>
                    <a href="{{action("\Kris\HR\Controllers\TaxController@edit",$tax->idtaxes)}}">
                        <i class="fa fa-pencil"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@stop