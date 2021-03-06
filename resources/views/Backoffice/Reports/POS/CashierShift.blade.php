@extends("Backoffice.Master")

@section("contents")
<?php 
$_credits = 0;
$_visa = 0;
$_cash = 0;
$_unclassified = 0;
$_roomposts= 0;
$_cash = 0;
?>

<div class="page-contents">

<div class="report-filter">
<table style="width:100%">
    <tr>
        <td><h3>Cashier Shift Summary </h3> </td>
        <td>
           <form style="float:right" action="" class="form-inline" method="get">
                <label>Date</label> 
                <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control"> - 
                <input name="enddate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control">
                <label>Cashier</label> 
                <select name="cashier" class="form-control">
                	 <option value="0">All</option>
                    <?php
                    $cashiers = App\FX::GetCashiers();
                    ?>

                    @if(isset($cashiers))
                        @foreach($cashiers as $cashier)
                            <option value="{{ $cashier->id }}">{{$cashier->username}}</option>
                        @endforeach

                    @endif
                </select>
                <input type="submit" class="btn btn-success btn-sm" value="Go">
                 <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" class="btn btn-default report-print-btn">Print</button>
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

@if(isset($data))
<?php $bill_by_status = array(); $paymodes = array(); ?>

<?php

foreach($pays as $pay){
	$paymodes[$pay->user_id]= $pay;
}
	
?>

@foreach($data as  $user_bill)
	<table class="table table-bordered table-stripped">
	<?php

		$bill_by_status[\ORG\Bill::SUSPENDED]  = 0;
		$bill_by_status[\ORG\Bill::CREDIT]  = 0;
		$bill_by_status[\ORG\Bill::PAID]  = 0;
		$bill_by_status[\ORG\Bill::ASSIGNED]  = 0;
        $cashier_sum =0;
		foreach ($user_bill as $bill) {
			switch ($bill->status) {
				case \ORG\Bill::SUSPENDED:
					$bill_by_status[\ORG\Bill::SUSPENDED] += $bill->bill_total;
					break;
				case \ORG\Bill::CREDIT:
					$bill_by_status[\ORG\Bill::CREDIT] += $bill->bill_total;
					break;
				case \ORG\Bill::PAID:
					$bill_by_status[\ORG\Bill::PAID] += $bill->paid;
					break;
				case \ORG\Bill::ASSIGNED:
					$bill_by_status[\ORG\Bill::ASSIGNED] += $bill->bill_total;
					break;
			}
            
            
			
		}

	?>

		<tr>
			<th rowspan="4">{{  $user_bill[0]->username }}</th>
			<th rowspan="3">Unclassified</th>
			<th rowspan="3">Credit</th>
			<th class="text-center" colspan="3">Paid</th>
			<th rowspan="3">Room Post</th>
		</tr>
			<tr>
				<td>Cash</td><td>Cards</td><td>Check</td>
			</tr>

			<tr>
				<td>{{ isset($paymodes[$user_bill[0]->user_id]) ? number_format($paymodes[$user_bill[0]->user_id]->cash) :
				"0" }}</td><td>{{ isset($paymodes[$user_bill[0]->user_id]) ?  number_format($paymodes[$user_bill[0]->user_id]->card) : "0" }}</td>
				<td>{{ isset($paymodes[$user_bill[0]->user_id]) ? number_format($paymodes[$user_bill[0]->user_id]->check_amount) : "0"}}</td>
			</tr>
		<tr>

            <?php
               
               $_cash +=isset($paymodes[$user_bill[0]->user_id]) ? $paymodes[$user_bill[0]->user_id]->cash : 0;
               $_visa +=isset($paymodes[$user_bill[0]->user_id]) ? $paymodes[$user_bill[0]->user_id]->card : 0;
               
               $cashier_sum +=isset($paymodes[$user_bill[0]->user_id]) ? $paymodes[$user_bill[0]->user_id]->cash : 0;
               $cashier_sum +=isset($paymodes[$user_bill[0]->user_id]) ? $paymodes[$user_bill[0]->user_id]->card : 0;
               
               $_unclassified +=$bill_by_status[\ORG\Bill::SUSPENDED];
               $_credits +=$bill_by_status[\ORG\Bill::CREDIT];
               $_roomposts +=$bill_by_status[\ORG\Bill::ASSIGNED];
              
            ?>
       
			<td>{{ number_format($bill_by_status[\ORG\Bill::SUSPENDED]) }}</td>
			<td>{{ number_format($bill_by_status[\ORG\Bill::CREDIT]) }}</td>
			<td class="text-center" colspan="3">{{ number_format($cashier_sum) }}</td>
			<td>{{ number_format($bill_by_status[\ORG\Bill::ASSIGNED]) }}</td>
		</tr>
	</table>
@endforeach
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Unclassified</th>
                <th>Credit</th>
                <th>Room Posts</th>
                <th>Cards</th>
                <th>Cash</th>
            </tr>
        </thead>

        <tr>
                <td>{{ number_format($_unclassified) }} </td>
                <td> {{ number_format($_credits) }}</td>
                <td> {{ number_format($_roomposts) }}</td>
                <td>{{ number_format($_visa) }} </td>
                <td>{{ number_format($_cash) }}</td>
            </tr>
    </table>
@endif

<div class="text-center print-footer">
       <table style="margin-bottom:85px;width:100%;" class="table">
           <tr>
               <td>
                   Cashier
               </td>

               <td>
                   CONTROLLER
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
    
</div>

@stop