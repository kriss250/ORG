@extends("HR::Master")

@section("contents")


<div class="col-md-10 main-contents">
    <div class="page-title">
        <h3>List of Departments</h3>
        <p style="font-size:12px;opacity:.6;margin-top:-5px;">Use this form to create a new employee and set their respective departments</p>
    </div>
    <div class="row" style="padding:10px 35px;">

        <table class="blue-table table table-condensed table-bordered table-striped">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Posts</th>
                    <th>Description</th>
                    <th>Active</th>
                    <th>Action</th>
                </tr>
            </thead>

            @foreach(\Kris\HR\Models\Department::all() as $dp)
            <tr>
                <td>{{$dp->iddepartments}}</td>
                <td>{{$dp->name}}</td>
                <td>{{count($dp->post)}}</td>
                <td>{{$dp->description}}</td>
                <td>Active</td>
                <td>
                    <a href=""><i class="fa fa-trash"></i></a>
                    <a href="{{action("\Kris\HR\Controllers\DepartmentController@edit",$dp->iddepartments)}}">
                        <i class="fa fa-pencil"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@stop