@extends("HR::Master")

@section("contents")


<div class="col-md-10 main-contents">
    <div class="page-title">
        <h3>Create a new bank</h3>
        <p style="font-size:12px;opacity:.6;margin-top:-5px;">Use this form to create a new employee and set their respective departments</p>
    </div>
    <div class="row" style="padding:10px 35px;">
        <form action="{{action('\Kris\HR\Controllers\BankController@store')}}" method="post" class="col-md-5">

            <input type="hidden" name="_token" value="{{csrf_token()}}" />
            <input type="hidden" name="_bank" value="{{isset($bank) ? $bank->idbanks : " 0" }}" />

            <label>Bank Name</label>
            <input value="{{isset($bank) ? $bank->bank_name : "" }}" type="text" name="name" placeholder="Enter Full name" class="form-control" />

            <label>Address</label>
            <input value="{{isset($bank) ? $bank->address : " " }}" name="address" type="text" class="form-control" />

            <label>Description</label>
            <textarea name="description" class="form-control">{{isset($bank) ? $bank->description : " " }}</textarea>
            <p>&nbsp;</p>
            <input type="submit" class="btn btn-success" value="Save" />
        </form>
    </div>
</div>
@stop