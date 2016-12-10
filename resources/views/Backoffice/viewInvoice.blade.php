@extends('Backoffice.Single')

@section("contents")
  <?php $total=0;$VAT = 0; ?>
<style>
    .page-contents
    {
      padding: 8px;
      font-size: 12px
    }

    .invoice-table td {
      font-size: 12px;
    }

    tr.empty td {
      text-indent: -9999px;
    }

    .invoice-table {
      color:rgb(10,10,10);
    }

    .invoice-items-table textarea.form-control {
        height:27px;
        resize:none;
        font-size: 13px;
    }

    .print-logo-wrapper {
      float: left
    }

    .logo {
      display: table;
      float: left;
    }

    .invoice-address p {
      margin-bottom: 1px
    }

    .logo-text {
      float: left;
      padding-left: 10px;
      display: table;
    }

    .logo-text p {
      margin: 0;
      font-size: 10px
    }

    .logo-text h3 {
      margin-bottom: 2px;
      font-size: 16px;
      margin-top: 0
    }

.table.invoice-table tr td {
  border-bottom:none !important;
  border-top: none !important;
  border-right: 1px solid;
  border-left: 1px solid;
  font-size: 10px;
}
.invoice-address {
  margin-bottom: 10px;
  padding: 10px;
  font-size:11px;
  line-height: 1.1
}


.invoice-table tfoot th:not(:empty) {
  background: #f2f2f2;
  border:1px solid;
}

.invoice-table thead th {
  border-color:#000 !important;
  border:1px solid;
}


.invoice-table {
  border-top:1px solid;
}

.invoice-table tr:last-child td {
  border-bottom: 1px solid !important;
}
.invoice-table tfoot th:empty{
  border:none;
}

.invoice-table tfoot tr:last-child th{
  font-size: 18px;
}
</style>

<div class="page-contents" style="padding:25px">
    <div class="header-print">
        <div class="print-logo-wrapper">
            <img class="logo" width="120" src="data:image/jpeg;base64,{{base64_encode($hotel->logo)}}" />
            <div class="logo-text">
                <h3>{{$hotel->hotel_name}}</h3>
                <p>Phone: {{$hotel->phone1}} / {{$hotel->phone2}}</p>
                <p>Email: {{$hotel->email1}}</p>
                <p>Address:{{$hotel->address_line1}}</p>
                <p>TIN : {{$hotel->TIN}}</p>
            </div>
        </div>

        <div class="print-header-desc text-right">
            <h3 class="invoice-no" style="font-weight:bold;margin-bottom:0px;color:rgb(125, 125, 125)">INVOICE N<sup>o</sup></h3>
            <h4 style="margin-top:0">{{$invoice->idinvoices}} / {{ (new Carbon\Carbon($invoice->created_at))->format("Y")}}</h4>
            <h5 style="border:1px solid rgb(100,100,100);display:inline-block;padding:10px">DATE : {{(new Carbon\Carbon($invoice->created_at))->format("d/m/Y")}}</h5>
        </div>

        <div class="clearfix"></div>
    </div>


    <h2 class="text-center"><strong>INVOICE: {{$invoice->idinvoices}} / {{ (new Carbon\Carbon($invoice->created_at))->format("Y")}}</strong></h2>

<div style="padding-left:5px" class="container-fluid">

  <div class="invoice-address col-xs-5">
     
    <p style="padding:3px 6px;border-bottom:2px solid;margin-bottom:8px;font-size:16px;">
      CUSTOMER
    </p>
    <p style="font-size:16px;padding-left:0"><b>{{ucfirst($invoice->institution)}}</b></p>
    <p style="margin-top:5px">{{$invoice->address}}</p>

    <p>&nbsp;</p>
    <p style="font-size:13px;font-weight:bold;margin-bottom:15px;">Description : {{$invoice->description}}</p>
    <p>Due Date : {{$invoice->due_date}}</p>
  </div>

</div>

<table class="table table-condensed invoice-table">

<thead>
  <tr>
    <th>Date</th>
    <th>Description</th>
    <th>Days/Nights</th>
    <th>Quantity</th>
    <th>Unit Price</th>
    <th>Total</th>
  </tr>
</thead>
<?php $min = 11; $itemsno = $invoice->items->count(); ?>

@foreach($invoice->items as $item)
  <tr>
  <td>
      {{$item->date}}
  </td>
  <td style="width:40%">{{$item->description
}}</td>
  <td style="width:50px">{{$item->days}}</td>
  <td style="width:50px">{{$item->qty}}</td>
  <td>{{$item->unit_price}}</td>
  <td>{{number_format($item->qty*$item->unit_price*$item->days)}}</td>
</tr>
<?php $total += $item->qty*$item->days*$item->unit_price; ?>
@endforeach

@if($itemsno < $min)
  @for($i=1;$i<$min-$itemsno;$i++)
    <tr class="empty">
    <td>.</td>
    <td>.</td>

    <td></td>
    <td></td>
    <td></td>
    <td>
    </td>
  </tr>
  @endfor
@endif
<?php $VAT = ($total *18)/118; ?>

<tfoot>
  <tr style="font-size:12px">
    <th colspan="4"></th>
    <th>NET AMOUNT</th>
    <th>{{number_format($total-$VAT)}}</th>
  </tr>

  <tr style="font-size:12px">
    <th colspan="4"></th>
    <th>VAT</th>
    <th>{{number_format($VAT)}}</th>
  </tr>

  <tr>
    <th colspan="4"></th>
    <th>TOTAL</th>
    <th>
<em style="font-size:10px;">{{$hotel->currency}}</em> {{number_format($total)}}</th>
  </tr>
</tfoot>
</table>


<?php
$spell = new NumberFormatter("en", NumberFormatter::SPELLOUT);
?>

<p style="text-transform:capitalize">
  <b>Total Amount : {{$spell->format($total)}} (VAT Inclusive) </b>
</p>

<div class="row" style="padding:15px;">

    <div class="col-xs-6" style="border:1px solid;padding:6px">
      <b>TIN/VAT</b> : {{$hotel->TIN}}<br />
     <b>Account Number</b> : Bank of Kigali 053-07719298-63 Rwf / 053-07719298-64
    </div>
    <div style="float:right" class="col-xs-4">
        <p>Done at {{$hotel->city}}, On {{(new Carbon\Carbon($invoice->created_at))->format("d/m/Y")}},</p>
      {{\Auth::user()->user_title}}<br />
      {{\Auth::user()->firstname}} {{\Auth::user()->lastname}}

    </div>
</div>
</div>

@stop
