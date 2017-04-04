@extends("HR::Master")

@section("contents")


<div class="col-md-10 main-contents">
    <div class="page-title">
        <h3>Create a new department</h3>
        <p style="font-size:12px;opacity:.6;margin-top:-5px;">Use this form to create a new employee and set their respective departments</p>
    </div>
    <div class="row" style="padding:10px 35px;">
        <form method="post" action="{{action('\Kris\HR\Controllers\DepartmentController@store')}}" class="col-md-5">
            <label>Department Name</label>
            <input type="hidden" name="_token" value="{{csrf_token()}}" />
            <input type="hidden" name="_department" value="{{isset($department) ? $department->iddepartments : "0" }}" />
            <input value="{{isset($department) ? $department->name : "" }}" type="text" name="name" placeholder="Enter Full name" class="form-control" />
            <label>Description</label>
            <textarea name="description" class="form-control">{{isset($department) ? $department->description : "" }}</textarea>
            <p>&nbsp;</p>
            <input type="submit" class="btn btn-success" value="Save" />
        </form>
    </div>
</div>
@stop