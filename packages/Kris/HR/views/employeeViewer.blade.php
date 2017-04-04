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
            <a class="btn btn-xs btn-default" href="">Edit</a>
        </li>

        <li>
            <a class="btn btn-xs btn-default" href="">Add Charge</a>
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
                <div class="row">
                    <div class="col-md-2" style="padding-right:0">

                        <img class="img img-thumbnail" src="/uploads/images/img.jpg" style="max-width:130px;width:100%" />
                    </div>
                    <div class="col-md-3">
                        Firstname : {{$employee->firstname}}
                        <br />
                        Last name : {{$employee->lastname}}
                        <br />
                        Gender : {{$employee->gender}}
                        <hr />
                        Education : Bachelors
                    </div>
                    <div class="col-md-4">
                        <p>Department : {{$employee->department->name}}</p>
                        <p>Post : {{$employee->post->name}}</p>
                        <hr />
                    </div>
                    <div class="col-md-3 text-right">
                        <p style="font-size:22px">
                            Salary :
                            <b>{{count($employee->salary) > 0 ? number_format($employee->salary[count($employee->salary)-1]->amount) : "N/A"}}</b>
                        </p>
                        <p>Avarage Day Rate : 0</p>
                    </div>
                </div>
                <hr />
                <div class="row">
                    <div class="col-md-4">
                        <p>WORK SCHEDULE</p>
                        <form class="form form-inline">
                            <label>Days Per Week</label>
                            <br />
                            <input type="number" min="1" max="7" class="form-control" />
                            <input type="submit" value="Save" class="btn btn-warning" />
                        </form>
                    </div>

                    <div class="col-md-3">
                        <p>CONTACTS</p>
                        @foreach($employee->contact as $contact)
                        <p><i class="fa fa-phone"></i> {{$contact->phone1}} / {{$contact->phone2}} </p>
                        <p><i class="fa fa-envelope"></i> {{$contact->email1}} / {{$contact->email2}} </p>
                        @endforeach
                    </div>

                    <div class="col-md-3">
                        <p>ADDRESS</p>
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
