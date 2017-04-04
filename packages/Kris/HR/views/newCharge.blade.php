@extends("HR::Master")

@section("contents")


<div class="col-md-10 main-contents">
    <div class="page-title">
        <h3>Create a new Charge</h3>
        <p style="font-size:12px;opacity:.6;margin-top:-5px;">Use this form to create a new employee and set their respective departments</p>
    </div>
    <div class="row" style="padding:10px 35px;">
        <form action="{{action('\Kris\HR\Controllers\ChargeController@store')}}" method="post" class="col-md-5">
            <input type="hidden" name="_token" value="{{csrf_token()}}" />
            <input type="hidden" name="_charge" value="{{isset($charge) ? $charge->idcharges : " 0" }}" />

            <label>Charge Name</label>
            <input value="{{isset($charge) ? $charge->charge_name : " 0" }}" required name="name" type="text" placeholder="Enter Full name" class="form-control" />
            <label>Charge Type</label>
            <select name="type" required class="form-control">
                <option value="">Choose Type</option>
                <option {{isset($charge) && $charge->charge_type == \Kris\HR\Models\Type::FIXED ? "selected"  : "" }} value="{{\Kris\HR\Models\Type::FIXED }}">Fixed</option>
                <option {{isset($charge) && $charge->charge_type == \Kris\HR\Models\Type::PERCENT ? "selected"  : "" }} value="{{\Kris\HR\Models\Type::PERCENT }}">Percent (%)</option>
            </select>
            <label>Re-occurency</label>
            <select name="occurancy" class="form-control" required>
                <option value="">Choose</option>
                <option {{isset($charge) && $charge->re_occurancy_id == \Kris\HR\Models\Occurancy::ONETIME ? "selected"  : "" }}  value="{{\Kris\HR\Models\Occurancy::ONETIME}}">One-Time</option>
                <option {{isset($charge) && $charge->re_occurancy_id == \Kris\HR\Models\Occurancy::WEEKLY ? "selected"  : "" }} value="{{\Kris\HR\Models\Occurancy::WEEKLY}}">Weekly</option>
                <option {{isset($charge) && $charge->re_occurancy_id == \Kris\HR\Models\Occurancy::MONTHLY ? "selected"  : "" }} value="{{\Kris\HR\Models\Occurancy::MONTHLY}}">Monthly</option>
                <option {{isset($charge) && $charge->re_occurancy_id == \Kris\HR\Models\Occurancy::YEARLY ? "selected"  : "" }} value="{{\Kris\HR\Models\Occurancy::YEARLY}}">Yearly</option>
            </select>
            <label>Value</label>
            <input  value="{{isset($charge) ? $charge->value : " 0" }}" required name="value" type="text" class="form-control" />
            <label>Description</label>
            <textarea name="description" class="form-control">{{isset($charge) ? $charge->description : " 0" }}</textarea>
            <p>&nbsp;</p>
            <input type="submit" class="btn btn-success" value="Save" />
        </form>
    </div>
</div>
@stop