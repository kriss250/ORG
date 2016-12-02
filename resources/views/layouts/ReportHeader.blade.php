<?php
//logo
//site name
//contacts
//email

$resto = \App\Resto::get()->first();
?>
<style>
    .report_header {
        border-bottom:1px dashed #828282;
        padding:6px;
        display:none;
        margin-bottom:10px;
    }

    .report_header .col-md-2 {
        padding:0
    }
    .DTTT_Print .report_header {
        display:block !important
    }
    .report_header .col-md-10 {
        padding:5px
    }
    .header_desc p {
        margin:0;
        color:#505050
    }

    .DTTT_Print .grid > .row {
        margin:0
    }
    .DTTT_Print .contents, .DTTT_Print .grid {
        width:99%;
        display:block;
        max-width:99%
    }
</style>
<div class="container-fluid report_header">
    <div class="col-md-9">
        <div class="col-md-2">
            <img width="100" src="{{ \ORG\Settings::$LOGO }}" />
        </div>
        <div class="col-md-10 header_desc">
            <p><b>{{$resto->resto_name}}</b></p>
            <p>{{$resto->resto_email }}</p>
            <p>{{$resto->resto_phone}}</p>
            <p>POS Report - {{ \ORG\Dates::$RESTODATE }}</p>
        </div>
    </div>

    <div class="col-md-3">
        <p style="margin:0" class="text-right">ORG System</p>
        <p class="text-right"><a href="">{{ $resto->website }}</a></p>
        <p class="text-right">User : {{Auth::user()->username }}</p>
    </div>
</div>
