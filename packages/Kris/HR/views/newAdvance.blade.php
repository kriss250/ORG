@extends("HR::Master")

@section("contents")


<div class="col-md-10 main-contents">
    <div class="page-title">
        <h3>Employee Advance</h3>
        <p style="font-size:12px;opacity:.6;margin-top:-5px;">Use this form to create a new employee and set their respective departments</p>
    </div>
    <div class="row" style="padding:10px 35px;">
        <form action="{{action('\Kris\HR\Controllers\AdvanceController@store')}}" method="post" class="col-md-5">
            <input type="hidden" name="_token" value="{{csrf_token()}}" />
            <label>Employee</label>
            <select required name="employee" class="form-control">
                <option value="">Select Employee</option>
                @foreach(\Kris\HR\Models\Employee::all() as $emp)
                <option value="{{$emp->idemployees}}">{{$emp->idemployees}}--{{$emp->firstname}} {{$emp->lastname}}</option>
                @endforeach
            </select>
<label>Amount</label>
            <input class="form-control" type="text" name="amount" value="" required />
            <label>Description</label>
            <textarea name="description" class="form-control"></textarea>
            <p>&nbsp;</p>
            <input type="submit" class="btn btn-success" value="Save" />
        </form>
    </div>
</div>
@stop