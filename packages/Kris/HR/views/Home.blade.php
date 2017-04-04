@extends("HR::Master")

@section("contents")

<script>
    $(document).ready(function(){

        $('.emp-chart').highcharts({
            chart: {
                plotBackgroundColor: "none",
                plotBorderWidth: null,
                plotShadow: false,
                type: 'line'
              
            },
            title : "DSDS",
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            credits: {
                enabled: false
            },
            plotOptions: {
                line: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '{point.name}',
                        style: {
                            color: '#3f3f3f',
                            'font-weight':"normal"
                        },
                        connectorColor: 'silver',
                        dotColor:"red"
                    }
                }
            },
            series: [{
                name: 'Percentage',
                data: JSON.parse('{!!\Kris\HR\Models\Salary::avgSalaryPerDepartmentChart()!!}')
            }]
        });

        $(".highcharts-background").attr("fill","none");
    })
</script>

    <div class="col-md-10 main-contents">
        <!---->
        <div class="col-md-8" style="padding-right:0">
            <ul class="status-list">
                <li class="vc_status">
                    <p>TOTAL EMPLOYEES</p>
                    <span>39</span> Employees
                    <i class="fa fa-star"></i>
                </li>


                <li class="rs_status">
                    <p>ABSENT EMPLOYEES</p>
                    <span>1</span>Employees
                    <i class="fa fa-calendar"></i>
                </li>


                <li class="oc_status">
                    <p>UPCOMING LEAVES</p>
                    <span>2</span>Rooms
                    <i class="fa fa-male"></i>
                </li>
                <li class="co_status">
                    <p>EXPIRED CONTRACTS</p>
                    <span>1</span>Rooms
                    <i class="fa fa-luggage"></i>
                </li>
            
            </ul>
            <div class="clearfix"></div>
            <div class="table-filter">
                
                <span style="padding-bottom:4px;padding-top:8px;display:block;text-transform:uppercase;font-size:11px;opacity:.5">Find Employee</span>
                <form class="form form-inline">
                    <input type="text" class="form-control" placeholder="Code #" />
                    <input type="text" class="form-control" placeholder="Employee name..." />
                    <input type="submit" value="Find" class="btn btn-success" />
                    <button type="button" class="btn btn-default pull-right"><i class="fa fa-plus"></i> Create New</button>
                </form>
            </div>
            <table class="blue-table table table-condensed table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Birthdate</th>
                        <th>Age</th>
                        <th>Phone</th>
                        <th>Department</th>
     
                    </tr>
                </thead>
                @foreach(\Kris\HR\Models\Employee::all() as $emp)
                    <tr ondblclick="javascript:location.href='{{action("\Kris\HR\Controllers\EmployeeController@show",$emp->idemployees)}}'">
                        <td>{{$emp->idemployees}}</td>
                        <td>{{$emp->firstname}} {{$emp->middlename}} {{$emp->lastname}}</td>
                        <td>{{$emp->gender}}</td>
                        <td>{{$emp->birthdate}}</td>
                        <td>{{(new \Carbon\Carbon(date("Y-m-d")))->diffInYears(new \Carbon\Carbon($emp->birthdate))}}</td>
                        <td>{{$emp->contact[0]->phone1}}</td>
                        <td>{{$emp->department->name}}</td>
                    </tr>
                
                @endforeach

            </table>
</div>
   
        <div class="col-md-4">
            <div class="blue-gradient-box pull-right">
                <h2 style="margin-top:10px" class="the-title container-fluid">
                    <b>EMPLOYEES COUNT</b>
                    <span>68</span>
                </h2>
                <hr class="blue-line" />

                <div class="emp-chart"></div>
            </div>
        </div>
    </div>

@stop