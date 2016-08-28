@extends('Backoffice.Master')

@section("contents")

<div class="page-contents">
    <h2>Create Invoice</h2>
    @if(\Session::has("msg"))
    <div class="alert alert-success">
        <button data-toggle="dismiss" class="btn alert-dismiss">
            <i class="fa fa-times"></i>
        </button>
        {{\Session::get("msg")}}
    </div>
    @endif
    
</div>

@stop
