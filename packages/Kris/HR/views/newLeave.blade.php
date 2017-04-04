@extends("HR::Master")

@section("contents")


<div class="col-md-10 main-contents">
    <div class="page-title">
        <h3>Create a new leave</h3>
        <p style="font-size:12px;opacity:.6;margin-top:-5px;">Use this form to create a new employee and set their respective departments</p>
    </div>
    <div class="row" style="padding:10px 35px;">
        <form class="col-md-5">
            <label>Employee</label>
            <select class="form-control"></select>

            <label>Leave type</label>
            <select class="form-control"></select>

            <label>Dates</label>
            <input type="text" placeholder="Enter Full name" class="form-control" /> to

            <label>Description</label>
            <textarea class="form-control"></textarea>
            <p>&nbsp;</p>
            <input type="submit" class="btn btn-success" value="Save" />
        </form>
    </div>
</div>
@stop