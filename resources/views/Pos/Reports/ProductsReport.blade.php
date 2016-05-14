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
<h2>Products Report </h2>
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
    <form action="{{ route("POSReportsPOST",'ProductsReport') }}" method="post">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
            Store <select name="store"><option>All</option>
            <?php $stores = \DB::select("select * from store") ; ?>

            @foreach($stores as $store)
                <option value="{{ $store->idstore }}"> {{$store->store_name }}</option>
            @endforeach
            </select>
            Date <input type="date" name="date" id="datefield" />
            <input type="submit" value="Generate" class="btn btn-sm btn-danger" />
        <a hrer="#" onclick="printF();" style="float:right" class="btn btn-sm btn-success">Print</a>
        </form>
</div>

<div class="clearfix"></div>
<br />
@if(isset($data))
<table class="table-bordered table">
<thead>
    <tr>
        <th>Product</th><th>U.P </th><th>Qty</th><th>Total</th><th>Store</th>
    </tr>
</thead>
<?php $GT = 0; ?>

    @foreach($data as $row)
    <tr>
    <td> {{$row->product_name}} </td>
    <td> {{$row->unit_price }} </td>
    <td> {{$row->qty }}</td>
    <td> {{ ($row->qty * $row->unit_price) }} </td>
    <?php $GT +=($row->qty * $row->unit_price); ?>
    <td> {{ $row->store_name }} </td>
    </tr>
    @endforeach

    @if(isset($free))
    <tr><td>{{ number_format($GT-$free) }}</td></tr>
    @endif
</table>
@endif
@stop