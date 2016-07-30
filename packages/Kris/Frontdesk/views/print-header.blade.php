<?php
$hotel  = \Kris\Frontdesk\Property::get()->first();
?>

<style>
    body {
        background: #575757;
        font-family: 'Segoe ui' !important;
    }
</style>
<div class="print-header">
    <div class="print-logo-wrapper">
        <img class="logo" width="100" src="data:image/jpeg;base64,{{base64_encode($hotel->logo)}}" />
        <div class="logo-text">
            <h3>{{$hotel->hotel_name}}</h3>
            <p>Phone: {{$hotel->phone1}} / {{$hotel->phone2}}</p>
            <p>Email: {{$hotel->email1}}</p>
            <p>Address: {{$hotel->address_line1}}</p>
            <p>TIN : {{$hotel->TIN}}</p>
        </div>
    </div>

    <div class="print-header-desc text-right">
        <h2 style="font-weight:bold;margin-bottom:0px">BILL NOTE</h2>
        <h4 style="margin-top:0">{{$res->idreservation}}</h4>
    </div>

    <div class="clearfix"></div>
</div>