@extends('Backoffice.Single')

@section("contents")
  <?php $total=0;$VAT = 0; ?>
<style>
    .page-contents
    {
      padding: 8px;
      font-size: 13px
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
      font-size: 12px
    }

    .logo-text h3 {
      margin-bottom: 2px;
      font-size: 18px;
      margin-top: 0;
      font-weight:bold
    }

.table.invoice-table tr td {
  border-bottom:none !important;
  border-top: none !important;
  border-right: 1px solid;
  border-left: 1px solid;
  font-size: 11px;
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
  font-size: 16px;
}

    .s-title {
        padding: 1px 0px;
        border-bottom: 1px dashed;
        margin-bottom: 18px;
        font-size: 11px;
        font-weight: bold;
        display:block;
        width: 50%;
        
    }
</style>

<div class="page-contents" style="padding:25px">
    <div class="header-print">
        <div class="print-logo-wrapper">
            <img class="logo" width="100" src="{{count(\App\Settings::get("logo")) > 0 ? \App\Settings::get("logo")[0] : "" }}" />
            <div class="logo-text">
                <h3>{{\App\Settings::get("name")}}</h3>
                <p>Phone: {{\App\Settings::get("phones")[0]}} / {{\App\Settings::get("phones")[1]}}</p>
                <p>Email: {{\App\Settings::get("email")}}</p>
                <p>Website : {{\App\Settings::get("website")}}</p>
                <p>Address:{{\App\Settings::get("state")}} - {{\App\Settings::get("city")}} </p>
                <p>TIN : {{\App\Settings::get("tin")}}</p>
            </div>
        </div>

        <div class="print-header-desc text-right">
            <h3 class="invoice-no" style="font-weight:bold;margin-bottom:0px;color:rgb(125, 125, 125)">
                INVOICE <br />
                {{$invoice->code}} / {{ (new Carbon\Carbon($invoice->created_at))->format("Y")}}
            </h3>
      
            <h5 style="border:1px solid rgb(100,100,100);display:inline-block;padding:10px">DATE : {{(new Carbon\Carbon($invoice->due_date))->format("d/m/Y")}}</h5>
        </div>

        <div class="clearfix"></div>
    </div>

<div style="padding-left:5px" class="container-fluid">

  <div class="invoice-address col-xs-8">
     
    <p style="margin-top:18px;" class="s-title">
      CUSTOMER
    </p>
      <br />
    <p style="font-size:16px;padding-left:0"> <b>Name : {{ucfirst($invoice->institution)}}</b></p>
    <p style="margin-top:5px">Address : {!! nl2br( htmlentities($invoice->address)) !!}</p>

    <p>&nbsp;</p>
    <p style="font-size:13px;font-weight:bold;margin-bottom:15px;">Description : {{$invoice->description}}</p>
    <!--<p>Due Date : {{ (new \Carbon\Carbon($invoice->due_date))->format("d/m/Y")}}</p>-->
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
  <td>{{number_format($item->unit_price)}}</td>
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

<p style="text-transform:capitalize;margin-top:-80px;max-width:68%">
  <b>Total Amount : {{$spell->format($total)}} RWF (VAT Inclusive) </b>
</p>

<div class="row" style="padding:15px;">

    <div class="col-xs-6" style="border:1px solid;padding:6px;font-size:11px">
      <b>TIN/VAT</b> : {{\App\Settings::get("tin")}}<br />
     <b>Account Number</b> : 
        <?php $accs=[]; $accs =  \App\Settings::get("bankaccount"); ?>
        @foreach($accs as $account)
        {{$account}}
        @endforeach
    </div>
    <div style="float:right;margin-top:40px" class="col-xs-4">
        <p>Done at {{\App\Settings::get('city')}}, On {{(new Carbon\Carbon($invoice->created_at))->format("d/m/Y")}},</p>
         General Manager<br />
      <!--{{\Auth::user()->firstname}} {{\Auth::user()->lastname}}-->

    </div>
</div>
</div>

@stop
