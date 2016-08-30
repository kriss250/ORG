@extends('Backoffice.Master')

@section("contents")


<div class="page-contents">
    <h2>Invoice {{$invoice->idinvoices}}/{{(new \Carbon\Carbon($invoice->created_at))->format("Y")}} Payment(s)</h2>
    <p>
      Customer : <b>{{$invoice->institution}}</b>
    </p>

    <a class="btn btn-primary btn-xs" href="{{action("InvoiceController@index")}}">Go to Invoices</a>
    <a class="btn btn-success btn-xs" href="{{action("InvoicePaymentController@create")}}?id={{$invoice->idinvoices}}">New Payment</a>

    <hr />
    <table class="table table-condensed table-bordered table-striped">
      <thead>
        <tr>
          <th>
            Date
          </th>
          <th>
            Description
          </th>
          <th>
            Amount
          </th>
          <th>
            W VAT
          </th>
          <th>
            WHT
          </th>
          <th>
            Pay mode
          </th>
          <th>
            <i class="fa fa-trash"></i>
          </th>
        </tr>
      </thead>
<?php $totalAmount =0; $vat  =0; $wht = 0; ?>
      @foreach($invoice->payment as $payment)
        <tr>
          <td>{{$payment->created_at}}</td>
          <td>{{$payment->description}}</td>
          <td>{{number_format($payment->amount)}}</td>
          <td>{{number_format($payment->wh_vat)}}</td>
          <td>{{number_format($payment->wht)}}</td>
          <td>
            {{\App\PayMode::find($payment->pay_mode)->method_name}}
          </td>

          <td>
            <a class="btn btn-danger btn-xs" href="{{action("InvoicePaymentController@delete",$payment->idpayment)}}"><i class="fa fa-trash"></i></a>
          </td>
        </tr>

        <?php $totalAmount += $payment->amount; $vat +=$payment->wh_vat; $wht +=$payment->wht;   ?>
      @endforeach
      <tfoot>
        <tr>
          <th colspan="2">TOTAL</th>
          <th>{{number_format($totalAmount)}}</th>
          <th>{{number_format($vat)}}</th>
          <th>{{number_format($wht)}}</th>
          <th></th>
          <th></th>
        </tr>
      </tfoot>

    </table>

</div>

@stop
