@extends("HR::Master")

@section("contents")


<div class="col-md-10 main-contents">
    <div class="page-title">
        <h3>Create a new tax</h3>
        <p style="font-size:12px;opacity:.6;margin-top:-5px;">Use this form to create a new employee and set their respective departments</p>
    </div>
    <div class="row" style="padding:10px 35px;">
        <form action="{{action('\Kris\HR\Controllers\TaxController@store')}}" method="post" class="col-md-5">

            <input type="hidden" name="_token" value="{{csrf_token()}}" />
            <input type="hidden" name="_tax" value="{{isset($tax) ? $tax->idtaxes : " 0" }}" />


            <label>Tax Name</label>
            <input value="{{isset($tax) ? $tax->tax_name : "" }}" required type="text" name="name" placeholder="Enter Full name" class="form-control" />
            <label>Tax Type</label>
            <select name="type" required class="form-control">
                <option value="">Choose Type</option>
                <option {{isset($tax) && $tax->type_id == \Kris\HR\Models\Type::FIXED ? "selected"  : "" }} value="{{\Kris\HR\Models\Type::FIXED }}">Fixed</option>
                <option {{isset($tax) && $tax->type_id == \Kris\HR\Models\Type::PERCENT ? "selected"  : "" }} value="{{\Kris\HR\Models\Type::PERCENT }}">Percent (%)</option>
            </select>
            <label>Re-occurency</label>
            <select name="occurancy" class="form-control" required>
                <option value="">Choose</option>
                <option {{isset($tax) && $tax->re_occurancy_id == \Kris\HR\Models\Occurancy::ONETIME ? "selected"  : "" }} value="{{\Kris\HR\Models\Occurancy::ONETIME}}">One-Time</option>
                <option {{isset($tax) && $tax->re_occurancy_id == \Kris\HR\Models\Occurancy::WEEKLY ? "selected"  : "" }} value="{{\Kris\HR\Models\Occurancy::WEEKLY}}">Weekly</option>
                <option {{isset($tax) && $tax->re_occurancy_id == \Kris\HR\Models\Occurancy::MONTHLY ? "selected"  : "" }} value="{{\Kris\HR\Models\Occurancy::MONTHLY}}">Monthly</option>
                <option {{isset($tax) && $tax->re_occurancy_id == \Kris\HR\Models\Occurancy::YEARLY ? "selected"  : "" }} value="{{\Kris\HR\Models\Occurancy::YEARLY}}">Yearly</option>
            </select>
            <label>Value</label>
            <input value="{{isset($tax) ? $tax->value : "" }}" name="value" type="text" class="form-control" />
            <p>&nbsp;</p>
            <input type="submit" class="btn btn-success" value="Save" />
        </form>
    </div>
</div>
@stop