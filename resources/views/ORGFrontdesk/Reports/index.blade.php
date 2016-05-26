<?php
if (Auth::attempt(["username"=>"SYS","password"=>"&123Pass"])) {
    $_username = Input::get('user');
    Session::put("fo_user",$_username);
}
?>
@extends("ORGFrontdesk.Reports.Master")

@section("contents")
<div style="text-transform: uppercase;padding:55px 25px;
color: #ccc;
text-align: center">
    <img style="display:none" src="/assets/images/backoffice_logo.png" width="80" />
    <h1 style="font-size: 62px;margin-bottom:0">Please Choose a Report</h1>
    <p>ORG Frontdesk Reports</p>
</div>
@stop