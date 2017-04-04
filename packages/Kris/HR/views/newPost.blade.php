@extends("HR::Master")

@section("contents")


<div class="col-md-10 main-contents">
    <div class="page-title">
        <h3>Create a new post</h3>
        <p style="font-size:12px;opacity:.6;margin-top:-5px;">Use this form to create a new employee and set their respective departments</p>
    </div>
    <div class="row" style="padding:10px 35px;">
        <form class="col-md-5" method="post" action="{{action('\Kris\HR\Controllers\PostController@store')}}">
            <input type="hidden" name="_token" value="{{csrf_token()}}" />
            <input type="hidden" name="_post" value="{{isset($post) ? $post->idposts : " 0" }}" />
            <label>Post Name</label>
            <input value="{{isset($post) ? $post->name : "" }}" required name="name" type="text" placeholder="Enter Full name" class="form-control" />
            <label>Department</label>
            <select name="department" required class="form-control">
                <option value="">Choose Departement</option>
                @foreach(\Kris\HR\Models\Department::all() as $dp)
                    <option {{(isset($post) && $post->department_id==$dp->iddepartments ? "selected" :"")}} value="{{$dp->iddepartments}}">{{$dp->name}}</option>
                @endforeach
            </select>
            <label>Description</label>
            <textarea name="description" class="form-control">{{isset($post) ? $post->description:"" }}</textarea>
            <p>&nbsp;</p>
            <input type="submit" class="btn btn-success" value="Save" />
        </form>
    </div>
</div>
@stop