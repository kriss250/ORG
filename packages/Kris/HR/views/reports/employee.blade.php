@extends("Frontdesk::MasterIframe")

@section("contents")
<div class="report-filter">
    <form action="" method="get">
        <div style="width:100%;max-width:980px;margin:auto" class="row">
            <div class="col-xs-3">
                <h4>HR Reports</h4>
            </div>

            <div style="font-size:11px !important" class="col-xs-9 container-fluid text-right">

                <div class="col-xs-3">
                    <select name="department" required class="form-control">
                        <option value="0">Choose Departement</option>
                        @foreach(\Kris\HR\Models\Department::all() as $dp)
                        <option {{(isset($post) && $post->department_id==$dp->iddepartments ? "selected" :"")}} value="{{$dp->iddepartments}}">{{    $dp->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-2">
                    <Select name="gender" class="form-control">
                        <option value="0">Choose Gender</option>
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                </div>


                  <div class="col-xs-2">
                    <Select name="education" class="form-control">
                        <option value="0">Education</option>
                        <option value="{{\Kris\HR\Models\Degree::NONE}}">None</option>
                        <option value="{{\Kris\HR\Models\Degree::PRIMARY}}">Primary School</option>
                        <option value="{{\Kris\HR\Models\Degree::SECONDARY}}">Secondary School</option>
                        <option value="{{\Kris\HR\Models\Degree::COLLEGE}}">College</option>
                        <option value="{{\Kris\HR\Models\Degree::BACHELOR}}">Bachelors'</option>
                        <option value="{{\Kris\HR\Models\Degree::MASTERS}}">Masters</option>
                        <option value="{{\Kris\HR\Models\Degree::DOCTOR}}">Doctorate</option>
                        <option value="{{\Kris\HR\Models\Degree::PROFESSOR}}">Professor</option>

                    </select>
                </div>

                <div class="col-xs-3">  
                    <input type="submit" class="btn btn-success btn-sm" name="go" value="Go" />
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
        <?php $x = 1;
              

              if(isset($_GET['go']))
              {
                  $emps =    \Kris\HR\Models\Employee::where("active","1");
                  if(isset($_GET['department']) && $_GET['department'] > 0)
                  {
                      $emps = $emps->where("department_id",$_GET['department']);
                  }

                  if(isset($_GET['gender']) && $_GET['gender']  !="0")
                  {
                      $emps = $emps->where("gender",strtolower( $_GET['gender']));
                  }

                  if(isset($_GET['education']) && $_GET['education']  !="0")
                  {
                      $emps = $emps->where("highest_degree", $_GET['education']);
                  }
                  $emps = $emps->get();
              }else {
                  $emps = \Kris\HR\Models\Employee::all();
              }
        ?>
        @foreach($emps as $emp)
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