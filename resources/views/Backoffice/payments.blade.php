@extends('Backoffice.Master')

@section("contents")
<div class="page-contents">

<div class="report-filter">
<table style="width:100%">
    <tr>
        <td><h3>POS Payments </h3> </td>
        <td>
          <form style="float:right" action="" class="form-inline" method="get">
                <label>Date</label> 
                <input name="startdate" type="text" class="date-picker form-control">
               <input name="enddate" type="text" class="date-picker form-control">
                <input type="submit" class="btn btn-success btn-sm" value="Go">
                 <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="POS Payments" class="btn btn-default report-print-btn">Print</button>
           </form> 
        </td>
    </tr>
</table>


</div>

<script type="text/javascript">

$(document).ready(function(){
	$(".delete-item-btn").click(function(e){
		e.preventDefault();
		var payid = $(this).attr("data-id");
		var bill_id = $(this).attr("data-bill-id");
		if(!confirm("Are you sure you want to delete this payment ?")){
			return false;
		}
		$.ajax({
			url : "{{action("PaymentsController@destroy")}}",
			type: "delete",
			data:"_token={{csrf_token()}}&id="+payid+"&bill_id="+bill_id,
			success: function(data)
			{
				if(data=="1")
				{
					$(".tr_"+ payid).remove();
				}else{
					alert("Error removing payment");
				}
			}
		})
	})
})
		

</script>
<p>Cash & Visa </p>
<input name="_method" type="hidden" value="DELETE">
<table class="table table-bordered table-stripped">
<thead>
	<tr>
		<th>Order ID</th><th>Customer</th><th>Amount</th><th>Mode</th><th>Comment</th><th>Cashier</th><th>Date</th>
	</tr>
</thead>
@foreach($pays as $pay)
	<tr class="tr_{{ $pay->idpayments }}">
		<td>{{ $pay->bill_id }}</td>
		<td>{{ $pay->customer }}</td>
		<?php
			$method= '';
			$total=0;


			if($pay->cash > 0)
			{
				$method = "cash";
				$total +=$pay->cash;
			}

			if($pay->bank_card>0)
			{
				$method = "Visa";
				$total += $pay->bank_card;
			}

			if($pay->check_amount>0)
			{
				$method="Check";
				$total +=$pay->check_amount;
			}

			if($pay->cash ==0 && $pay->bank_card==0 && $pay->check_amount ==0)
			{
				$method= "Credit";
			}

		?>
		<td>{{ number_format($total) }}</td>
		<td> {{$method }}</td>
		<td> {{$pay->comment}}</td>
		<td> {{$pay->username }}</td>
		<td>{{ $pay->date }}</td>
		<td>
			<a class="text-danger delete-item-btn" data-bill-id="{{$pay->bill_id }}" data-id="{{$pay->idpayments }}"  href="#"><i class="fa fa-trash-o"></i></a>
		</td>
	</tr>
@endforeach

</table>

<p>Room Post</p>

<table class="table table-bordered table-stripped">
<thead>
	<tr>
		<th>Order ID</th><th>Room</th><th>Amount</th><th>Cashier</th><th>Date</th>
	</tr>
</thead>

@if(isset($bills) && count($bills)>0)
	@foreach($bills as $bill)
	<tr>
		<td>{{ $bill->idbills }}</td><td>{{ $bill->room }}</td><td>{{ number_format($bill->bill_total) }}</td><td>{{ $bill->username }}</td><td>{{ \App\FX::DT($bill->date) }}</td>
	</tr>

	@endforeach
@else 
	<tr>
		<td colspan="5">There were no room posts at the specified date</td>
	</tr>
@endif

</table>
</div>


<div class="clearfix"></div>
@stop
