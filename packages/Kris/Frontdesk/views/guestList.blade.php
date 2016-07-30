@extends("Frontdesk::MasterIframe")
@section("contents")
<div class="panel-desc">
    <p class="title">Guest Database</p>
    <p class="desc"></p>
</div>
<div class="list-filter">
    <?php $wd = \Kris\Frontdesk\Env::WD(); ?>
    <form action="" method="post" class="form-inline">
        <input type="hidden" name="_token" value="{{csrf_token()}}" />
    
        <fieldset>
            <label>Guest Names</label>
            <input type="text" name="todate" value="" class="form-control" placeholder="Names of the guest" />
        </fieldset>


        <input type="submit" value="Find" class="btn btn-success btn-xs" />
    </form>
   
    <div class="clearfix"></div>
</div>

<div class="clearfix"></div>
<br />
<?php

$guests  =  !isset($guests)  || is_null($guests) ? \Kris\Frontdesk\Guest::limit("20")->get() : $guests;
?>
<table class="table table-bordered table-condensed data-table table-striped text-left">
    <thead>
        <tr>
            <th>#ID</th>
            <th>Firstname</th>
            <th>Lastname</th>
            <th>ID/Passport</th>
            <th>Birthdate</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Country</th>
            <th><i class="fa fa-edit"></i></th>
        </tr>
    </thead>

    @if(!empty($guests))
    @foreach($guests as $guest )

    <tr>
        <td>{{$guest->id_guest}}</td>
        <td class="text-left">{{$guest->firstname}} </td>
        <td>{{$guest->lastname}}</td>
        <td>{{$guest->id_doc}}</td>
        <td>{{$guest->birthdate}}</td>
        <td>{{$guest->phone}}</td>
        <td>{{$guest->email}}</td>
        <td>{{$guest->country}}</td>

        <td>
            <a class="btn btn-xs btn-default" href="{{action('\Kris\Frontdesk\Controllers\GuestController@edit',$guest->id_guest)}}">
                <i class="fa fa-edit"></i>
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

@stop