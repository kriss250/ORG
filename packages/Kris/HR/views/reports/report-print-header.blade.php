<?php

$hotel  = \Kris\Frontdesk\Property::get()->first();
$_startdate = isset($_GET['startdate'])? new \Carbon\Carbon($_GET['startdate']) : \Kris\Frontdesk\Env::WD() ;
$_enddate =isset($_GET['enddate'])? new \Carbon\Carbon($_GET['enddate']) : \Kris\Frontdesk\Env::WD();

?>

<style>
    body,html {
        background: #575757 !important;
        font-family: 'Segoe ui' !important;
    }
</style>
<div class="print-header">
    <div class="print-logo-wrapper">
        <img class="logo" width="100" src="data:image/jpeg;base64,{{base64_encode($hotel->logo)}}" />
        <div class="logo-text">
            <h3>{{$hotel->hotel_name}}</h3>
            <p style="font-size:12px">Phone: {{$hotel->phone1}} / {{$hotel->phone2}}</p>
            <p>Email: {{$hotel->email1}}</p>
            <p>TIN : {{$hotel->TIN}}</p>
        </div>
    </div>

    <div class="print-header-desc text-right">
        <h5 style="font-weight:bold;margin-bottom:8px">{{$_startdate->eq($_enddate) ? $_startdate->format("d/m/Y"): $_startdate->format("d/m/Y")." - ".$_enddate->format("d/m/Y") }}</h5>
        <p>Printed by {{\Kris\Frontdesk\User::me()->firstname}} {{\Kris\Frontdesk\User::me()->lastname}}</p>
        <p>Printed at {{\Kris\Frontdesk\Env::WD()->format("d/m/Y")}} {{date("H:i:s")}}</p>
    </div>

    <div class="clearfix"></div>
</div>