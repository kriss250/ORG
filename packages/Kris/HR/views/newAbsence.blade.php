@extends("HR::Master")

@section("contents")


<div class="col-md-10 main-contents">
    <div class="page-title">
        <h3>Absence</h3>
        <p style="font-size:12px;opacity:.6;margin-top:-5px;">Use this form to create a new employee and set their respective departments</p>
    </div>
    <div class="row" style="padding:10px 35px;">
        <form method="post" action="{{action("\Kris\HR\Controllers\AbsenceController@store")}}" class="col-md-5">
            <label>Employee</label>
            <input type="hidden" name="_token" value="{{csrf_token()}}" />
            <select required name="employee" class="form-control">
                <option value="">Select Employee</option>
                @foreach(\Kris\HR\Models\Employee::all() as $emp)
                <option value="{{$emp->idemployees}}">{{$emp->idemployees}}--{{$emp->firstname}} {{$emp->lastname}}</option>
                @endforeach
            </select>

            <label>Dates</label>
            <div class="row">
                <div class="col-md-6">
                    <label>From</label>
                    <input required name="startdate" type="text" placeholder="Start Date" class="form-control datepicker" />
                </div>

                <div class="col-md-6">
                    <label>To</label>
                    <input required name="enddate" type="text" placeholder="End Date" class="form-control datepicker" />
                </div>
            </div>
            

            <label>Description</label>
            <textarea name="description" required class="form-control"></textarea>
            <p>&nbsp;</p>
            <input type="submit" class="btn btn-success" value="Save" />
        </form>
    </div>
</div>
@stop