<script type="text/javascript">

	$(document).ready(function(){
		var url = "{{ action('CashbookController@show',$cashbook->cashbookid)}}";

		$('.content-container').on('apply.daterangepicker',".date-picker-single", function(ev, picker) {
			//$(".content-container").prepend("<p class='text-center'>Loading ...</p>");
			refresh(url);
			return true;
		});
	})
</script>

<div class="content-container">


<table class="table">
	<tr>
		<td>
			@if(isset($cashbook))
				<h3> {{ $cashbook->cashbook_name }} </h3>
				<b style="font-size: 18px;padding:8px 0">Balance: {{ number_format($cashbook->balance) }} </b>
			@endif
		</td>

		<td style="max-width: 70px;">
			Date
			<div style="max-width: 180px;" class="input-group">
			<input type="text" class="form-control date-picker-single" />
			<span class="input-group-addon">
				<i class="fa fa-calendar"></i>
			</span>
			</div>
		</td>
	</tr>
</table>
<br><br>


<table class="table table-bordered table-stripped">
 	<thead>
 		<tr>
 		  <th>ID</th>
 		  <th>Date</th>
 		  <th>Motif(Reason)</th>
 		  <th>User</th>
 		  
 		  <th>IN</th>
 		  <th>OUT</th>
 		  <th>Balance</th>
 		  <th>Actions</th>
 		</tr>
 	</thead>

    <tr style="font-weight:bold">
        <td colspan="7">Initial Balance</td> <td>0</td>
    </tr>
 	<?php
 	$INs = 0;
	$OUTs =0; 

 	?>
	@foreach($transactions as $transaction)
		<tr>
            
			<td>{{ $transaction->transactionid }} </td>
			<td> {{ \App\FX::DT($transaction->date) }} </td>
			<?php 
				$IN = $transaction->type=="IN" ? $transaction->amount : 0; 
				$OUT= $transaction->type=="OUT" ? $transaction->amount : 0; 
				$INs += $IN;
				$OUTs +=$OUT;  
			?>
			
			<td> {{ $transaction->motif }} </td>
			<td> {{ $transaction->username }} </td>
			
			<td>{{ number_format($IN) }} </td>
			<td>{{ number_format($OUT) }}</td>
			<td>{{ number_format($transaction->new_balance) }}</td>
			<td>
				<button data-refresh-url="{{ action('CashbookController@show',$cashbook->cashbookid)}}" data-url="{{ action("CashbookTransactionController@update",$transaction->transactionid) }}/?type={{ $transaction->type }}&cashbook={{ $cashbook->cashbookid }}&amount={{ $transaction->amount }}" style="background: none;font-size: 14px;color:red" class="delete-trans-btn btn btn-sm"><i class="fa fa-trash-o"></i></button>
			</td>
		</tr>
	@endforeach
	<tr>
	<td style="font-weight: bold;" colspan="4">CLOSING BALANCE</td>
	<td><b>{{ number_format($INs) }}</b></td>
	<td><b>{{ number_format($OUTs) }}</b></td>
     <td><b>{{ number_format($INs-$OUTs) }}</b></td>
	</tr>
</table>
</div>


