@extends("HR::Master")

@section("contents")


<div class="col-md-10 main-contents">
    <div class="page-title">
        <h3>Employees' List</h3>
        <p style="font-size:12px;opacity:.6;margin-top:-5px;">Use this form to create a new employee and set their respective departments</p>
    </div>
    <div class="row" style="padding:10px 35px;">

        <table class="blue-table table table-condensed table-bordered table-striped">
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Birthdate</th>
                <th>Age</th>
                <th>Phone</th>
                <th>Nationality</th>
                <th>Father</th>
                <th>Mother</th>
                <th>Action</th>
            </tr>
        </thead>
            @foreach(\Kris\HR\Models\Employee::all() as $emp)
            <tr>
                <td>{{$emp->idemployees}}</td>
                <td>{{$emp->firstname}} {{$emp->middlename}} {{$emp->lastname}}</td>
                <td>{{$emp->gender}}</td>
                <td>{{$emp->birthdate}}</td>
                <td>{{(new \Carbon\Carbon(date("Y-m-d")))->diffInYears(new \Carbon\Carbon($emp->birthdate))}}</td>
                <td>{{$emp->contact[0]->phone1}}</td>
                <td>{{$emp->address[0]->country}}</td>
                <td>{{$emp->father_name}}</td>
                <td>{{$emp->mother_name}}</td>
                <td>
                    <a href=""><i class="fa fa-trash"></i></a>
                    <a href="{{action('\Kris\HR\Controllers\EmployeeController@edit',$emp->idemployees)}}">
                        <i class="fa fa-pencil"></i>
                    </a>
                </td>
            </tr>
            @endforeach

         </table>        
    </div>
</div>
@stop