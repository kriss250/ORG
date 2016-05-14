@extends("Backoffice.Master")

@section("contents")
<div class="page-contents">

<div class="report-filter">
<table style="width:100%">
    <tr>
        <td><h3>Cashbook ({{ $book_name }})</h3> </td>
        <td>
           <form style="float:right" action="" class="form-inline" method="get">
                <label>Date</label> 
                <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control"> - 
                <input name="enddate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control">
                <label>Cash Book </label>
                <select name="cashbook" class="form-control">
                    <?php
                    $cashbooks = \DB::connection("mysql_backoffice")->select("SELECT cashbookid,cashbook_name FROM cash_book order by cashbookid desc");
                    ?>

                    @if(isset($cashbooks))
                        @foreach($cashbooks as $book)
                            <option value="{{ $book->cashbookid }}">{{ $book->cashbook_name }}</option>
                        @endforeach
                    @endif
                </select>
                <input type="submit" class="btn btn-success btn-sm" value="Go">
                 <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['enddate']) ? $_GET['enddate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Cashbook ({{ $book_name }}) " class="btn btn-default report-print-btn">Print</button>
           </form> 
        </td>
    </tr>
    <tr>
       <td>
      <p class="text-danger"><b>Date : {{ isset($_GET['startdate']) && isset($_GET['enddate'])  ?  \App\FX::Date($_GET['startdate'])." - ".\App\FX::Date($_GET['enddate']) : \App\FX::Date(\ORG\Dates::$RESTODATE) }}</b></p>
      </td>
    </tr>
</table>


</div>

    @if(isset($transactions))
   
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

 		</tr>
 	</thead>

    <tr style="font-weight:bold">
        <td colspan="6">Initial Balance</td> <td>{{ number_format($initial) }}</td>
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
			
		</tr>
	@endforeach
	<tr>
	<td style="font-weight: bold;" colspan="4">CLOSING BALANCE</td>
	<td><b>{{ number_format($INs) }}</b></td>
	<td><b>{{ number_format($OUTs) }}</b></td>
     <td><b>{{ number_format($INs+$initial-$OUTs) }}</b></td>
	</tr>
</table>

    <div class="text-center print-footer">
       <table style="margin-bottom:85px;width:100%;" class="table">
           <tr>
               <td>
                   H. Cashier
               </td>

               <td>
                   C.F.O.O
               </td>

               <td>
                    ACCOUNTANT
               </td>

               <td>
                   DAF
               </td>

               <td>
                   G. MANAGER
               </td>
           </tr>
       </table>
        <div class="clearfix"></div>
    </div>

    @endif


    </div>


@stop