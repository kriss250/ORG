
@extends("Backoffice.Master")

@section("contents")
<div class="page-contents">



<div class="content-container">


<table class="tabl" style="width:100%">
	<tr>
		<td>
			@if(isset($cashbook))
				<h3> {{ $cashbook->cashbook_name }} </h3>
				<b style="font-size: 18px;padding:8px 0">Balance: {{ number_format($cashbook->balance) }} </b>
			@endif
		</td>

		<td style="max-width: 70px;text-align:right">
			<p>Switch Cashbooks</p>
		  <div class="btn-group">
               <?php
                $_cashbooks =  DB::connection("mysql_backoffice")->select("SELECT * FROM cash_book");
                ?>
              @foreach($_cashbooks as $_cashbook)
                <a href="{{ action("CashbookController@show",$_cashbook->cashbookid) }}" class="btn btn-success">{{ $_cashbook->cashbook_name }} </a>
              @endforeach
            </div>
		</td>
	</tr>
</table>
<br><br>

    <div style="border:1px solid rgb(230, 225, 225);padding:16px;margin-bottom:15px;background:rgb(245, 243, 243);">
        <p class="text-danger">New Transaction <b style="float:right;font-size: 14px;margin-right: 15px;">{{ strlen(\Session::get('date')) > 4 ? \Session::get('date')  : \App\FX::Date(\ORG\Dates::$RESTODATE) }}</b></p>

        <div class="clearfix"></div>
        <form class="form-inline"  action="{{ action("CashbookTransactionController@store") }}" method="post">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
       
        <label>Amount</label>
        <input required="" type="text" class="form-control" name="amount">
             <label>Motif</label>
                 <input required="" type="text" class="form-control" style="width:30%" name="motif">
         
        <input style="width:100px" class="form-control date-picker" type="text" name="date" value="{{ \ORG\Dates::$RESTODATE }}" />
          
        <input type="hidden" name="cashbook" value=" {{ $cashbook->cashbookid }}" />
        

           <label>Type</label>
        <select required="" name="type" class="form-control">
	        <option value="">Choose</option>
	        <option value="IN">IN</option>
	        <option value="OUT">OUT</option>
        </select>
   
        <input class="btn btn-danger" type="submit" name="submit" value="Save">
        </form>
    </div>
<table style="background:whitesmoke" class="table table-bordered table-stripped">
 	<thead>
 		<tr>
 		  <th>ID</th>
 		  <th>Date</th>
 		  <th>Motif(Reason)</th>
 		  <th>User</th>
 		  
 		  <th>IN</th>
 		  <th>OUT</th>
 		  <th>Balance</th>
 		  <th>Action</th>
 		</tr>
 	</thead>

    <tr style="font-weight:bold">
        <td colspan="6">Initial Balance</td> <td> {{ number_format($initial) }}</td>
    </tr>
 	<?php
 	$INs = 0;
	$OUTs =0; 
    $new_b = $initial;
    
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
			<td>

                <?php
                        $new_b +=($IN-$OUT); 
                        echo number_format($new_b);
                ?>
			</td>
			<td>
				<button data-refresh-url="{{ action('CashbookController@show',$cashbook->cashbookid)}}" data-url="{{ action("CashbookTransactionController@update",$transaction->transactionid) }}/?type={{ $transaction->type }}&cashbook={{ $cashbook->cashbookid }}&amount={{ $transaction->amount }}" style="background: none;font-size: 14px;color:red" class="delete-trans-btn btn btn-sm"><i class="fa fa-trash-o"></i></button>
			</td>
		</tr>

    
	@endforeach
	<tr>
	<td style="font-weight: bold;" colspan="4">CLOSING BALANCE</td>
	<td><b>{{ number_format($INs) }}</b></td>
	<td><b>{{ number_format($OUTs) }}</b></td>
     <td><b>{{ number_format($new_b) }}</b></td>
	</tr>
</table>
</div>


    </div>

@stop 

