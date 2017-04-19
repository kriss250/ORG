@extends("HR::Master")

@section("contents")


<div class="col-md-10 main-contents">

    <div class="page-title">
        <h3>
            {{$employee->firstname}} {{$employee->lastname}}
            <span style="font-size:14px;">(Code #{{$employee->idemployees}})</span>
        </h3>
    </div>
    <ul class="list-inline pull-right">
        <li>
            <a class="btn btn-xs btn-success" href="">Print Profile</a>
        </li>
        <li>
            <a class="btn btn-xs btn-default" href="">Deactivate</a>
        </li>
        <li>
            <a class="btn btn-xs btn-default" href="{{action('\Kris\HR\Controllers\EmployeeController@edit',$employee->idemployees)}}">Edit</a>
        </li>
    </ul>
    <div class="clearfix"></div>
    <div class="row" style="padding:10px 35px;">

        <ul class="grid nav nav-tabs" style="margin-top:-10px">
            <li class="active">
                <a data-toggle="tab" href="#pane-1">
                    General
                </a>
            </li>
            <li>
                <a data-toggle="tab" href="#pane-1">
                    Charges
                </a>
            </li>
            <li>
                <a data-toggle="tab" href="#pane-1">
                    Leaves
                </a>
            </li>
        </ul>

        <div class="tab-content body-tab-content" style="background:linear-gradient(to bottom,#f5f8ff,transparent)">

            <div class="tab-pane active" style="font-size:13px" id="pane-1">
                <div class="row" style="background:#dff8d2;padding:25px;margin-top:-15px;border-radius:6px">
                    <div class="col-md-2" style="padding-right:0">

                        <img class="img img-thumbnail" src="/uploads/images/avatar.png" style="max-width:130px;width:100%;background:rgba(255, 255, 255, 0.50)" />
                    </div>
                    <div class="col-md-3">
                        Firstname : {{$employee->firstname}}
                        <br />
                        Last name : {{$employee->lastname}}
                        <br />
                        Gender : {{$employee->gender}}
                        <hr style="border-color:#bee0ad" />
                        Education : 
                        <?php

                        switch($employee->highest_degree)
                        {
                            case \Kris\HR\Models\Degree::BACHELOR:
                                print "Bachelors";
                                break;

                            case \Kris\HR\Models\Degree::COLLEGE:
                                print "College Diploma";
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
                            case \Kris\HR\Models\Degree::NONE:
                                print "N/A";
                                break;

                            case \Kris\HR\Models\Degree::PROFESSOR:
                                print "Proffessorat";
                                break;
                            case \Kris\HR\Models\Degree::SECONDARY:
                                print "Secondary";
                                break;
                        }

                       
                        ?>

                        <p>Age : {{ (new \Carbon\Carbon($employee->birthdate))->diff(\Carbon\Carbon::now())->y }}</p>
                    </div>
                    <div class="col-md-4">
                        <p style="margin-bottom:1px">Department : {{$employee->department->name}}</p>
                        <p style="margin-bottom:1px">Post : {{$employee->post->name}}</p>
                        <p>Hired : {{$employee->hire_date}}</p>
                        <hr style="border-color:#bee0ad" />
                        @foreach($employee->contact as $contact)
                        <p style="margin-bottom:0">
                            <i class="fa fa-phone"></i> {{$contact->phone1}}  {{$contact->phone2}}
                        </p>
                        <p>
                            <i class="fa fa-envelope"></i> {{$contact->email1}}  {{$contact->email2}}
                        </p>
                        @endforeach
                    </div>
                    <div class="col-md-3 text-right">
                        <p style="font-size:22px">
                            Salary :
                            <b>{{count($employee->salary) > 0 ? number_format($employee->salary[count($employee->salary)-1]->amount) : "N/A"}}</b>
                        </p>
                        <p>Avarage Day Rate : 0</p>
                        <hr />
                        Status {!!$employee->active==1 ? "<b class='text-success'>Active</b>" : "<b class='text-danger'>Deactivated</b>"!!}
                    </div>
                </div>
                <hr />
                <div class="row">

                    <div class="col-md-3">
                        <p><b>Parents</b></p>
                        <p>Mother : {{$employee->mother_name}}</p>
                        <p>Father : {{$employee->father_name}}</p>
                    </div>

                    <div class="col-md-4">
                        <p><b>CONTRACT INFORMATION</b></p>
                        <form class="form form-inline">
                            <label>Start Date</label>
                            <br />
                            {{$employee->created_at}} to {{$employee->updated_at}}
                            
                        </form>
                    </div>

                  

                    <div class="col-md-3">
                        <p><b>ADDRESS</b></p>
                        @foreach($employee->address as $address)
                        <p>
                            country : {{$address->country}}
                        </p>
                        <p>
                            City : {{$address->city}}
                        </p>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        </div>

    </div>
        @stop
