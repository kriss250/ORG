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
        <img class="logo" width="100" src="{{\App\Settings::get('logo')[0]}}" />
        <div class="logo-text">
            <h3>{{\App\Settings::get("name")}}</h3>
            <p>Phone: {{\App\Settings::get('phones')[0]}} / {{\App\Settings::get('phones')[1]}}</p>
            <p>Email: {{\App\Settings::get('email')}}</p>
            <p>Address: {{$hotel->address_line1}}</p>
            <p>TIN : {{\App\Settings::get('tin')}}</p>
        </div>
    </div>

    <div class="print-header-desc text-right">
        <h2 style="font-weight:bold;margin-bottom:0px">BILL NOTE</h2>
        <h4 style="margin-top:0">{{$res->idreservation}}</h4>
    </div>

    <div class="clearfix"></div>
</div>