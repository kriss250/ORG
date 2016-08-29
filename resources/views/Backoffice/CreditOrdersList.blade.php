@extends('Backoffice.Master')

@section("contents")

<div class="page-contents">
<div class="report-filter">
  <h3>Orders on Credit</h3>
  <p>Orders</p>
</div>

<table class="table table-condensed table-bordered">
  <tdead>
    <tr>
      <th>Date</th>
      <th>Voucher</th>
      <th>Creditor</th>
      <th>Description</th>
      <th>Amount</th>
      <th>Paid</th>
      <th>Balance</th>
      <th>Action</th>
    </tr>
  </thead>

  @foreach($orders as $order)
    <tr>
      <td>{{$order->created_at}}</td>
      <td>{{$order->voucher}}</td>
      <td>{{$order->creditor->name}}</td>
      <td>{{$order->description}}</td>
      <td>{{number_format($order->amount)}}</td>
      <td>{{$order->paid_amount}}</td>
      <td>{{number_format($order->amount-$order->paid_amount)}}</td>
      <td><a class="btn btn-primary btn-xs" href=""><i class="fa fa-money"></i></a></td>
    </tr>
  @endforeach
</table>

</div>

@stop
