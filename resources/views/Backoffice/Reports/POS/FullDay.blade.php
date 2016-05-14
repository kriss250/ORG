@extends("Backoffice.Master")
 <?php  $visa = 0; $cash = 0; $credit = 0; $room_post = 0; $bill_totals = 0; ?>
@section("contents")


<?php

$bills = isset($sales) ? $sales['bills'] : null;
$bill_items = isset($sales) ? $sales['bill_items'] : null;
$bill_pay  = isset($sales) ? $sales['bill_pay'] : null;
?>
<div class="page-contents">
 
<div class="report-filter">
<table style="width:100%">
    <tr>
        <td><h3>Day Report (Full)</h3> </td>
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
                <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Day Report (Fulll)" class="btn btn-default report-print-btn">Print</button>
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

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Room</th>
            <th>Items</th>
            <th>Qty</th>
            <th>U.P</th>
            <th class="diff">Total</th>
            <th>Cash</th>
            <th>Visa</th>
            <th>Check</th>
            <th>Paid</th>
        </tr>
    </thead>
    @if(isset($bill_items) && count($bill_items)>0)
   
    @foreach($bill_items as $item)
    <tr>
        <td>{{ $item->idbills }} </td>
        <td>{{ $item->customer }} </td>
         <td>{{ $item->room }} </td>

        <td>- {!! implode('<br /> - ', explode(',',$item->product)) !!} </td>
        <td>{!! implode('<br />', explode(',',$item->quantity)) !!} </td>
        <td>{!! implode('<br />', explode(',',$item->unitprice)) !!}  </td>
   
        <td  class="diff">{{ $item->total }} </td>
        <?php $bill_totals +=  $item->total; ?>
        <td>{{ isset($bill_pay[$item->idbills]->cash) ? $bill_pay[$item->idbills]->cash : 0 }} </td>
        <td>{{ isset($bill_pay[$item->idbills]->bank_card) ? $bill_pay[$item->idbills]->bank_card : 0 }} </td>
  
        <td>{{ isset( $bill_pay[$item->idbills]->check_amount) ? $bill_pay[$item->idbills]->check_amount : 0}} </td>
        <td>{{ $item->paid }} </td>
    </tr>
    <?php 

	  $visa += isset($bill_pay[$item->idbills]->bank_card) ? $bill_pay[$item->idbills]->bank_card : 0; 
      $cash +=isset($bill_pay[$item->idbills]->cash) ? $bill_pay[$item->idbills]->cash : 0; 
      $credit += ($item->status==ORG\Bill::CREDIT) ? $item->total-$item->paid: 0;
      $room_post += ($item->status==ORG\Bill::ASSIGNED) ? $item->total : 0;
    ?>

    @endforeach
    <tr>
        <td colspan="6"><b>TOTAL</b></td>
        <td class="diff">
            {{ number_format($bill_totals) }}
        </td>
        <td>{{ number_format($cash) }}</td>
        <td>{{ number_format($visa) }}</td>
        <td>0</td>
        <td>{{ number_format($cash+$visa) }} </td>
    </tr>
    @else 
    <tr><td colspan="10">There is no data at the specified date</td></tr>
    @endif
</table>




<!-- CREDITS -->

<?php

$bills = isset($credits) ? $credits['bills'] : null;
$bill_items = isset($credits) ? $credits['bill_items'] : null;

$rooms =  isset($room_posts) ? $room_posts['rooms'] : null;
$guest = isset($room_posts) ? $room_posts['guest'] : null;
$items = isset($room_posts) ? $room_posts['items'] : null;
?>

    @if(isset($bills))
    <h4>CREDITS</h4>
    <table class="table-bordered table">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Order No.</th>
                <th>Cashier</th>
                <th>Item</th>
                <th>Qty</th>
                <th>U.Price</th>
                <th>Total</th>
                <th>Paid</th>
                <th>Balance</th>
            </tr>
        </thead>
       <?php $due = 0; $paid=0; ?>
        @foreach($bills as $bill)
        
            <?php $items_no = count($bill_items[$bill->idbills]); ?>
            <?php $i = 0; $due +=$bill->bill_total; $paid +=$bill->amount_paid; ?>
     
                @foreach($bill_items[$bill->idbills] as $item)
            

                <tr>
                    @if($i==0)
                    <td rowspan="{{ $items_no }}">
                        {{ $bill->customer }}
                    </td>
                

                    <td rowspan="{{ $items_no }}">
                        {{ $bill->idbills }}
                    </td>

                    <td rowspan="{{ $items_no }}">
                        {{ $bill->username }}
                    </td>
                    @endif
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ $item->unit_price }}</td>

                    @if($i==0)
                    <td rowspan="{{ $items_no }}">{{ number_format($bill->bill_total) }} </td>
                    <td rowspan="{{ $items_no }}">{{ number_format($bill->amount_paid) }} </td>
                    <td rowspan="{{ $items_no }}">{{ number_format($bill->bill_total-$bill->amount_paid) }} </td>
                    @endif
                </tr>
        <?php $i++; ?>
                @endforeach
           
      
        
        @endforeach
        <tr>
            <td colspan="6"><b>TOTAL</b></td>
            <td><b>{{ number_format($due) }}</b></td>
            <td><b>{{ number_format($paid) }}</b></td>
            <td><b>{{ number_format($due-$paid) }}</b></td>
        </tr>
    </table>
    @endif


<!-- ROOM POSTS -->
    <h4>Room Posts</h4>
@if(isset($rooms))
     
    <table class="table-bordered table">
<thead>
   <tr>
       <th>customer</th>
        <th>room</th>
        <th class="text-center" colspan="4">order</th>
        <th>Total</th>
    </tr> 
</thead>
    <?php $tt2=0; $GT=0; ?>

    @foreach($rooms as $room)
    <?php 
        $items_count = 0;
        $items_col = "";
        $tr = "";
        $room_sub_total = 0;

        foreach ($items[$room->room] as $item) { //loop through room bills
            $bill_sub_total = 0;
            $sub_items = explode(',', $item->item);
            $items_count+=2;
            $tr .= "<tr>
                        <th class='text-center' style='background:rgb(248, 248, 248)' colspan='4'>Order No. ".$item->bill_id."</th>
                    </tr>";

                foreach ($sub_items as $sub_item) { //loop  through each room bill items
                    list($item_name,$qty,$price) = explode(':', $sub_item);
                    $sub_total = 0;
                    $sub_total = $qty * $price;
                    $room_sub_total +=$sub_total;
                    $bill_sub_total += $sub_total;
                    $items_count+=1;
                    $tr .="<tr>
                            <td>$item_name</td>
                            <td>$qty</td>
                            <td>$price</td> 
                            <td>$sub_total</td> 
                        </tr>";
                }



             $tr .= "<tr> <td colspan='3'><b>T.P</b></td>
                            <th class='text-right'>".number_format($bill_sub_total)."</th>
                        </tr>";   

                       
         }
//$items_count++;

    ?>
    <tr class="room_end">
         @if(isset($guest))
             <td rowspan="{{ $items_count+1}}">{{ $guest[$room->room][0]->guest }} <p style="font-size:12px;">({{$guest[$room->room][0]->Account}})</p> </td>
        @endif
        <td rowspan="{{ $items_count+1}}">{{ $room->room}}</td>
        <td style="padding: 0"  colspan="4"> </td>
        <td rowspan="{{$items_count+1}}">{{ number_format($room_sub_total) }}</td>
    </tr>

    <?php $GT +=$room_sub_total; ?>

   {!! $tr !!}
    @endforeach

<tr>
<td colspan="6"><b>TOTAL</b></td>
<td><b>{{ number_format($GT) }}</b></td>
</tr>



</table>

@endif

<!-- Summary -->
    <h4>Summary</h4>
    @if(isset($bills) && count($bills)>0 )
   
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Debts (Unpaid)</th><th>Paid</th><th>Room Posts</th><th>Visa</th> <th>cash</th><th>Total Sales</th>
        </tr>
    </thead>
    <tr>
       
        <td>
            <!-- Debts -->
            {{ number_format($credit) }} 
        </td>
        <td>
            <!--Paid -->
            {{ number_format($cash+$visa) }}
        </td>

        <td>
            <!-- Room Posts -->
            {{ $room_post }}
        </td>

        <td>
            <!-- Visa -->
            {{ number_format($visa) }}

        </td>

        <td>
            <!-- Cash -->
             {{ number_format($cash) }}
        </td>

        <td> {{ number_format( $room_post + $credit +$visa+$cash) }}</td>
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