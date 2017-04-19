@extends("Frontdesk::MasterIframe")

@section("contents")
<div class="report-filter">
    <form action="" method="get">
        <div style="width:100%;max-width:980px;margin:auto" class="row">
            <div class="col-xs-5">
                <h4>HR Reports</h4>
            </div>

            <div class="col-xs-7 container-fluid text-right">

                <div class="col-xs-4">
                   
                    <input style="max-width:100%" name="startdate" type="text" value="{{\Kris\Frontdesk\Env::WD()->format("Y-m-d")}}" class="datepicker form-control" />-
                </div>
                <div class="col-xs-4">
                    <input name="enddate" type="text" value="{{\Kris\Frontdesk\Env::WD()->format("Y-m-d")}}" class="datepicker form-control" />
                </div>

                <div class="col-xs-3">  
                    <input type="submit" class="btn btn-success btn-sm" value="Go" />
                    <button type="button" onclick="window.print()" class="btn btn-default report-print-btn">Print</button>
                </div>
            </div>

        </div>
    </form>
</div>
<div class="print-document">
    @include("HR::reports.report-print-header")
    <p class="report-title">Employee Report</p>
    <table class="table table-bordered table-condensed table-striped">
        <thead>
            <tr>
                <td>#</td>
                <th>Code</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Birthdate</th>
                <th>Age</th>
                <th>Phone</th>
                <th>Father</th>
                <th>Mother</th>
                <th>Nationality</th>
                <th>Department</th>
                <th>Post</th>
                <th>Education</th>
            </tr>
        </thead>
        <?php $x = 1; ?>
        @foreach(\Kris\HR\Models\Employee::all() as $emp)
        <tr>
            <td>{{$x}}</td>
            <td>{{$emp->idemployees}}</td>
            <td>{{$emp->firstname}} {{$emp->middlename}} {{$emp->lastname}}</td>
            <td>{{$emp->gender}}</td>
            <td>{{$emp->birthdate}}</td>
            <td>{{(new \Carbon\Carbon(date("Y-m-d")))->diffInYears(new \Carbon\Carbon($emp->birthdate))}}</td>
            <td>{{$emp->contact[0]->phone1}}</td>
            <td>{{$emp->father_name}}</td>
            <td>{{$emp->mother_name}}</td>
            <td>{{$emp->address[0]->country}}</td>
          <td>{{$emp->department->name}}</td>
            <td>{{$emp->post->name}}</td>
            <td>
                <?php

    switch($emp->highest_degree)
    {
        case \Kris\HR\Models\Degree::NONE:
            print "N/A";
            break;
        case \Kris\HR\Models\Degree::BACHELOR:
            print "Bachelors";
            break;
        case \Kris\HR\Models\Degree::COLLEGE:
            print "Advanced Deiploma";
            break;
        case \Kris\HR\Models\Degree::DOCTOR:
            print "Doctorate";
            break;
        case \Kris\HR\Models\Degree::MASTERS:
            print "Masters";
            break;
        case \Kris\HR\Models\Degree::PRIMARY:
            print "Primary";
            break;
        case \Kris\HR\Models\Degree::PROFESSOR:
            print "Proffessor";
            break;
        case \Kris\HR\Models\Degree::SECONDARY:
            print "Secondary";
            break;
    }

    $x++;
                ?>
            </td>
        </tr>
        @endforeach

    </table>

    @include("HR::reports.report-footer")

</div>

@stop