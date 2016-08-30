@extends('Backoffice.Single')

@section("contents")
  <?php $total=0;$VAT = 0; ?>
<style>
    .invoice-items-table .form-control {
        border-color: transparent;
        height: 28px;
        font-size: 13px;
    }


    .page-contents
    {
      padding: 8px;
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
      font-size: 13px
    }

    .logo-text h3 {
      margin-bottom: 2px;
      margin-top: 0
    }

    .invoice-table-wrapper {
  margin: 0 !important;
  border: 1px solid rgb(200,200,200);
  display: block;
  padding: 0px;
  min-height: 560px;
}

.invoice-table-wrapper td {
  border:none
}

.invoice-address {
  border:1px solid;
  margin-bottom: 10px;
  padding: 15px;
}
</style>

<div class="page-contents" style="padding:25px">
    <div class="header-print">
        <div class="print-logo-wrapper">
            <img class="logo" width="100" src="data:image/jpeg;base64,{{base64_encode($hotel->logo)}}" />
            <div class="logo-text">
                <h3>{{$hotel->hotel_name}}</h3>
                <p>Phone: {{$hotel->phone1}} / {{$hotel->phone2}}</p>
                <p>Email: {{$hotel->email1}}</p>
                <p>Address:{{$hotel->address_line1}}</p>
                <p>TIN : {{$hotel->TIN}}</p>
            </div>
        </div>

        <div class="print-header-desc text-right">
            <h2 style="font-weight:bold;margin-bottom:0px;color:rgb(125, 125, 125)">INVOICE</h2>
            <h4 style="margin-top:0">{{$invoice->idinvoices}} / {{ (new Carbon\Carbon($invoice->created_at))->format("Y")}}</h4>
        </div>

        <div class="clearfix"></div>
    </div>


    <h2 class="text-center"><strong>INVOICE</strong></h2>

<div class="container-fluid">
  <div class="invoice-address col-xs-5" style='font-weight:bold'>
    To
    <p> </p>
    <p>{{$invoice->institution}}</p>
    <p>{{$invoice->address}}</p>

    <p></p>

    <p>Due Date : {{$invoice->due_date}}</p>
  </div>

</div>

<div class="invoice-table-wrapper">

<table class="table table-condensed">

<thead>
  <tr>
    <th>Date</th>
    <th>Description</th>
    <th>Unit Price</th>
    <th>Quantity</th>
    <th>Total</th>
  </tr>
</thead>

@foreach($invoice->items as $item)
  <tr>
  <td>{{(new \Carbon\Carbon($item->date))->format("d/m/Y") }}</td>
  <td>{{$item->description}}</td>

  <td>{{$item->unit_price}}</td>
  <td>{{$item->qty}}</td>
  <td>{{number_format($item->qty*$item->unit_price)}}</td>
</tr>
<?php $total += $item->qty*$item->unit_price; ?>
@endforeach

<?php $VAT = ($total *18)/118; ?>


</table>
{{number_format($VAT)}}
{{number_format($total-$VAT)}}
{{number_format($total)}}
<?php
$spell = new NumberFormatter("en", NumberFormatter::SPELLOUT);
?>
</div>
<p style="text-transform:capitalize">
  {{$spell->format($total)}}
</p>
</div>

@stop
