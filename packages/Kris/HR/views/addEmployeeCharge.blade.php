@extends("HR::Master")

@section("contents")


<div class="col-md-10 main-contents">
    <div class="page-title">
        <h3>Add charge to employee's account</h3>
        <p style="font-size:12px;opacity:.6;margin-top:-5px;">Use this form to create a new employee and set their respective departments</p>
    </div>
    <div class="row" style="padding:10px 35px;">
        <form method="post" action="{{action("\Kris\HR\Controllers\ChargeController@saveEmployeeCharge")}}" class="col-md-5">
            {!!isset($charge) ? '<input type="hidden" name="charge" value="'.htmlentities($charge->idemployee_charges).'" />'  : '' !!}
            <label>Employee</label>
            <input type="hidden" name="_token" value="{{csrf_token()}}" />
            <select required name="employee" class="form-control">
                <option value="">Select Employee</option>
                @foreach(\Kris\HR\Models\Employee::all() as $emp)
                <option {{isset($charge) && $charge->employee_id==$emp->idemployees ? "selected" : ""}} value="{{$emp->idemployees}}">{{$emp->idemployees}}--{{$emp->firstname}} {{    $emp->lastname}}</option>
                @endforeach
            </select>

            <label>Amount</label>
           <input type="text" required value="{{isset($charge) ? $charge->amount :""}}" name="amount" class="form-control" />

            <label>Date</label>
            <div class="row">
                <div class="col-md-6">
                    <input value="{{isset($charge) ? $charge->date :""}}" required name="date" type="text" placeholder="Start Date" class="form-control datepicker" />
                </div>

            </div>
            

            <label>Description</label>
            <textarea name="description" required class="form-control">{{isset($charge) ? $charge->description :""}}</textarea>
            <p>&nbsp;</p>
            <input type="submit" class="btn btn-success" value="Save" />
        </form>
    </div>
</div>
@stop