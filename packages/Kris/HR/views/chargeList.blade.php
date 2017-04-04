@extends("HR::Master")

@section("contents")


<div class="col-md-10 main-contents">
    <div class="page-title">
        <h3>List of registered charges</h3>
        <p style="font-size:12px;opacity:.6;margin-top:-5px;">Use this form to create a new employee and set their respective departments</p>
    </div>
    <div class="row" style="padding:10px 35px;">

        <table class="blue-table table table-condensed table-bordered table-striped">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Charge Name</th>
                    <th>Value</th>
                    <th>Occurancy</th>
                    <th>Creation Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            @foreach(\Kris\HR\Models\Charge::all() as $charge)
            <tr>
                <td>{{$charge->idcharges}}</td>
                <td>{{$charge->charge_name}}</td>
                <td>
                    {{$charge->value}}
                    (<?php 
                     switch($charge->charge_type)
                     {
                         case \Kris\HR\Models\Type::FIXED:
                             print "fixed";
                             break;
                         case \Kris\HR\Models\Type::PERCENT:
                             print "%";
                             break;
                     }
                     ?>)
                </td>
                <td>
                    <?php
                    switch($charge->re_occurancy_id)
                    {
                        case \Kris\HR\Models\Occurancy::ONETIME:
                            print "One-Time";
                            break;
                        case \Kris\HR\Models\Occurancy::WEEKLY:
                            print "Weekly";
                            break;
                        case \Kris\HR\Models\Occurancy::MONTHLY:
                            print "Monthly";
                            break;
                        case \Kris\HR\Models\Occurancy::YEARLY:
                            print "Yearly";
                            break;
                    }
                    ?>
                </td>
                <td>{{$charge->created_at}}</td>
                <td>
                    <a href=""><i class="fa fa-trash"></i></a>
                    <a href="{{action("\Kris\HR\Controllers\ChargeController@edit",$charge->idcharges)}}">
                        <i class="fa fa-pencil"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@stop