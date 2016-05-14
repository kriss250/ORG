@extends("Pos.master")

@section("printHeader")

@include("layouts/ReportHeader")

@stop

@section("contents")
<style>
    .report-options {
        padding: 8px;
        background: rgb(240, 240, 240) none repeat;
    }

    @media print {
        .report_header img {
            max-width: 60px !important;
        }

        .report_header {
            font-size: 11px !important;
            ;
        }

        .table-bordered {
            border-color: #000;
            display: table !important;
        }

        h2 {
            font-size: 14px;
        }

        .table-bordered tr td, .table-bordered tr th {
            border-color: #000 !important;
            font-size: 11px;
            padding: 1px !important;
            color: #000;
        }

        .col-md-2 {
            float: left !important;
            max-width: 100px;
        }

        .footer p {
            display: none !important;
        }

        .col-md-10 {
            max-width: 400px;
            float: left;
        }

        .report-options, .page_info, .header, .footer {
            display: none;
        }
    }

    .head-report {
        border-bottom:1px dashed;
        margin-bottom:10px;
        display:none
    }
    .head-report td 
    {
        width:33.333%;
        text-align:center
    }
</style>

<script>
    function printF()
    {
        $("body").addClass("DTTT_Print");
        window.print();
    }

    $(document).ready(function () {
        $("#datefield").datepicker({"dateFormat":"yy-mm-dd"});
    })

</script>
<table class="head-report" style="width:100%">
    <tr>
        <td style="text-align:left">
            <h2>POS Report</h2>
            <p>Classic Hotel</p>
            <p>02/02/2016</p>
            </td>
        <td>
            <img width="100" src="https://192.168.1.99/images/logo.jpg" />
            <br />
            
        </td>
        <td>
            
        </td>
    </tr>
</table>

<p class="page_info"><i class="fa fa-info-circle"></i> Please use the table below to navigate or filter the results. You can download the table as csv, excel and pdf.</p>

<div class="report-options">
    <form action="{{ route("POSGeneralReport") }}" method="post">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
            Cashier <select><option>All</option></select>
            Store <select><option>All</option></select>
            Date <input type="date" name="date" id="datefield" />
            <input type="submit" value="Generate" class="btn btn-sm btn-danger" />
        <a hrer="#" onclick="printF();" class="btn btn-xs btn-success">Print</a>
        </form>
</div>

<div class="clearfix"></div>
<br />
<h4>Room Posts</h4>
@if(isset($rooms))

    <table class="table-bordered table">
    <thead>
        <tr>
            <th>Room</th>
            <th>Order ID</th>
            <th>T.P</th>
        </tr>
    </thead>
    <?php $tt2=0; ?>
    @foreach($rooms as $room)
    <tr>
        <td>{{ $room->room}}</td>
          <td class="text-right">{!! implode('<br />  ', explode(',',$room->billid)) !!} </td>
        
          <td class="text-right">{!! implode('<br />  ', explode(',',$room->totals)) !!} </td>
        <?php 
        $its = explode(',',$room->totals);
        foreach($its as $it)
        {
            $tt2 += $it;
        }
        ?>
 
    </tr>
   
    @endforeach

    <tr><td style="text-align:right" colspan="3"><b>{{ number_format($tt2) }} Rwf</b></td></tr>
</table>

@endif
<h4>Credit Report</h4>
@if(isset($credits))
<table class="table-bordered table">
    <thead>
        <tr>
            <th>Customer</th>
            <th>Order ID</th>
            <th>T.P</th>
            <th>User</th>
        </tr>
    </thead>
    <?php $tt=0; ?>
    @foreach($credits as $credit)
    <tr>
        <td>{{ $credit->customer}}</td>
          <td class="text-right">{!! implode('<br />', explode(',',$credit->billid)) !!} </td>
        
          <td class="text-right">{!! implode('<br />', explode(',',$credit->totals)) !!} </td>
        
        <td>{{ $credit->username}}</td>

         <?php 
        $its = explode(',',$credit->totals);
        foreach($its as $it)
        {
            $tt += $it;
        }
        
        ?>

    </tr>
   
    @endforeach

    <tr><td style="text-align:right" colspan="3"> <b> {{ number_format($tt) }} Rwf</b> </td></tr>
</table>
@endif
@stop