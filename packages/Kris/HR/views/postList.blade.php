@extends("HR::Master")

@section("contents")


<div class="col-md-10 main-contents">
    <div class="page-title">
        <h3>List of Posts</h3>
        <p style="font-size:12px;opacity:.6;margin-top:-5px;">Use this form to create a new employee and set their respective departments</p>
    </div>
    <div class="row" style="padding:10px 35px;">

        <table class="blue-table table table-condensed table-bordered table-striped">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Description</th>
                    <th>Active</th>
                    <th>Action</th>
                </tr>
            </thead>

            @foreach(\Kris\HR\Models\Post::all() as $ps)
            <tr>
                <td>{{$ps->idposts}}</td>
                <td>{{$ps->name}}</td>
                <td>{{$ps->department->name}}</td>
                <td>{{$ps->description}}</td>
                <td></td>
                <td>
                    <a href=""><i class="fa fa-trash"></i></a>
                    <a href="{{action("\Kris\HR\Controllers\PostController@edit",$ps->idposts)}}">
                        <i class="fa fa-pencil"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@stop