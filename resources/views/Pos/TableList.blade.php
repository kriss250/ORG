@extends("Pos.master")

@section("contents")

 <h2>List Of Tables</h2>

<p class="page_info"><i class="fa fa-info-circle"></i> List of tables already registered.</p>

<br>
<table class="table table-striped table-bordered table-condensed">
    <thead>
        <tr>
            <th>Table Name</th>
            <th>Description</th>
            <th>Action</th>
        </tr>
    </thead>

    @foreach(\App\Table::where("active","1")->get() as $tb)
    <tr>
        <td>{{$tb->table_name}}</td>
        <td>{{$tb->description}}</td>
        <td><a style="font-size:15px;" class="text-danger" href="{{action("TableController@delete",$tb->idtables)}}"><i class="fa fa-trash"></i></a></td>
    </tr>
    @endforeach
</table>


@stop