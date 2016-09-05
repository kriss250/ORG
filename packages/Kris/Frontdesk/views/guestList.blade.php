@extends("Frontdesk::MasterIframe")
@section("contents")

<div class="list-filter">
    <?php $wd = \Kris\Frontdesk\Env::WD(); ?>
    <form action="" method="get" class="form-inline">

        <fieldset class="bordered" style="width:280px;">
            <label>Guest Names</label>
            <input type="text" name="guest_name" value="" class="form-control" placeholder="Names of the guest" />
        </fieldset>


        <input type="submit" value="Find" class="btn btn-success btn-xs" />
    </form>

    <div class="clearfix"></div>
</div>

<div class="clearfix"></div>

<?php

$guests  =  !isset($guests)  || is_null($guests) ? \Kris\Frontdesk\Guest::orderBy("id_guest","desc")->limit("20")->get() : $guests;
?>

<div class="list-wrapper">
    <p class="list-wrapper-title">
        <span>Guest Database</span>
    </p>

    <table class="table table-bordered table-condensed data-table table-striped text-left">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>ID/Passport</th>
                <!--<th>Birthdate</th>-->
                <th>Phone</th>
                <!--<th>Email</th>-->
                <th>Country</th>
                <th>
                    <i class="fa fa-edit"></i>
                </th>
            </tr>
        </thead>

        @if(!empty($guests))
    @foreach($guests as $guest )

        <tr>
            <td>{{$guest->id_guest}}</td>
            <td class="text-left">{{$guest->firstname}} </td>
            <td>{{$guest->lastname}}</td>
            <td>{{$guest->id_doc}}</td>
            <!--<td>{{$guest->birthdate}}</td>-->
            <td>{{$guest->phone}}</td>
            <!--<td>{{$guest->email}}</td>-->
            <td>{{$guest->country}}</td>

            <td style="min-width:50px">
                <a class="btn btn-xs btn-default" href="{{action('\Kris\Frontdesk\Controllers\GuestController@edit',$guest->id_guest)}}">
                    <i class="fa fa-edit"></i>
                </a>

                <a class="btn btn-xs btn-success" href="{{action('\Kris\Frontdesk\Controllers\StatementsController@guest',$guest->id_guest)}}">
                    <i class="fa fa-file-o"></i>
                </a>
            </td>
        </tr>
        @endforeach
    @else
        <tr>
            <td colspan="9">
                No data
            </td>
        </tr>
        @endif
    </table>
</div>
@stop
