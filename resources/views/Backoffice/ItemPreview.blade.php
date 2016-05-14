<?php use \App\FX; ?>
@if(strtolower($_GET['type'])=="bills")
    <?php
    $id = $_GET['id'];
    
    $bill = \DB::select("SELECT idbills,customer,product_name,bill_total,print_count,last_printed_by,deleted_by,bill_items.unit_price,bill_items.qty,amount_paid,change_returned,bills.pay_date ,status_name, deleted,bills.date as bill_date,username as cashier,waiter_name FROM org_pos.bill_items 
        join bills on idbills = bill_id
        join products on products.id = product_id
        join users on users.id = bills.user_id
        join bill_status on status_code = status
        join waiters on waiters.idwaiter = waiter_id
        where bill_id=?",[$id]);
    
    $pays = \DB::select("select check_amount,bank_card,cash,username,void,payments.date from payments join users on users.id=user_id where bill_id=?",[$id]);
    
    ?>
    
    
    @if(count($bill)>0)

    
<table class="table">
    <tr><td class="text-center" colspan="2"><b>Status : {{ $bill[0]->status_name }}</b></td></tr>
    <tr>
        <td>Order ID : <b>{{$bill[0]->idbills }}</b><br />
            Date : {{ FX::DT($bill[0]->bill_date) }}
        </td>
        <td class="text-right">
            Payment Date  : {{ FX::DT($bill[0]->pay_date) }}<br />
            Customer : {{ $bill[0]->customer }}
        </td>
    </tr>
</table>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Sub Total</th>
                </tr>
            </thead>
            <?php $total = 0; ?>
        @foreach($bill as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->unit_price }}</td>
                <td>{{ $item->qty }}</td>
                <td>{{ number_format($item->unit_price*$item->qty) }}</td>
            </tr>
           
        @endforeach
            <tr>
                <td class="text-center" colspan="4">{{ $bill[0]->bill_total }}</td>
            </tr>
        </table>

<p><b>Amount Paid {{ $bill[0]->amount_paid }}</b></p>
<p>Change Returned {{ $bill[0]->change_returned }} </p>

@if(count($pays)>0)
<strong>Bill Payment history</strong>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Cash</th>
            <th>Bank Card</th>
            <th>Check</th>
            <th>User</th>
            <th>Cancelled</th>
            <th>Date</th>
        </tr>

    </thead>

    @foreach($pays as $pay)
        <tr>
            <td>{{ number_format($pay->cash) }}</td>
            <td>{{ number_format($pay->bank_card) }}</td>
            <td>{{ number_format($pay->check_amount) }}</td>
            <td>{{ $pay->username }}</td>
            <td>{!!  $pay->void==1 ? '<b class="text-danger">Yes</b>': '<b class="text-success">No</b>' !!}</td>
            <td>{{ FX::DT($pay->date) }}</td>
        </tr>
    @endforeach

</table>

@endif

<div style="border:1px solid #ccc;padding-top:10px" class="row">
    <div class="col-md-6">
        Cashier : {{ $bill[0]->cashier }} 
        <p>Printed Copies : {{ $bill[0]->print_count }}</p>
    </div>

    <div class="col-md-6">
        Waiter : {{ $bill[0]->waiter_name }}
       <p> Is Deleted : {!! $bill[0]->deleted==1 ? "<b class='text-danger'>Yes</b>" : "No" !!}</p>
        @if($bill[0]->deleted==1)
            <b class="text-danger">Deleted by : {{ \DB::select("select username from users where id=?",[$bill[0]->deleted_by])[0]->username }} </b> 
        @endif
    </div>
</div>



    @endif

<button data-url="{{ action("BillsController@destroy") }}" data-id="{{ $bill[0]->idbills }}" {{ ($bill[0]->deleted==1) ? "disabled='disabled'" : "" }} style="margin: 20px auto;display: block;" class="delete_bill_btn btn btn-sm btn-danger">DELETE BILL</button>

@endif