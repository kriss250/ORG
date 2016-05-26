@extends('Backoffice.Master')

@section("contents")
<div class="single-page-contents">
    <h3>
        List of Users
    </h3>
    <p>The list below contains system users, that are registered to access ORG system applications : Frontdesk , Stock, POS, Backoffice , Health Center</p>
    <br />
    <table class="table table-bordered table-striped table-condensed">
        <thead>
            <tr>
                <th>Username</th>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>Registration Date</th>
                <th>Group</th>
                <th>Level</th>
                <th>Allowed Access</th>
                <th>Reset</th>
            </tr>
        </thead>
        <?php
        $users = array_merge($stock_users,$pos_users,$fo_users);
        ?>

        @foreach($users as $user)
        <tr>
            <td>{{$user->username}}</td>
            <td>{{$user->firstname}}</td>
            <td>{{$user->lastname}}</td>
            <td></td>
            <td>{{isset($user->group_name) ? $user->group_name : ""}}</td>
            <td>{{isset($user->level) ? $user->level : ""}}</td>
            <td>
              {{$user->system}}
            </td>
            <td> <a href="{{action("UniversalUsersController@edit",$user->id)}}" class="btn btn-xs btn-danger"><i class="fa fa-key" aria-hidden="true"></i></a></td>
        </tr>
        @endforeach
    </table>
</div>


@stop