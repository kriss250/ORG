@extends("Backoffice.Master")
 <?php  $visa = 0; $cash = 0; $credit = 0; $room_post = 0; $bill_totals = 0; ?>
@section("contents")
<div class="page-contents">
 <script>
     $(document).ready(function () {
         <?php $_store_id = isset($_GET['store']) ? $_GET['store'] : 0; ?>

         var sub_title = $("select[name='store']").children("[value='{{$_store_id}}']").html();
         $(".title_dsc").html("(" + sub_title + ")");
         var title = $(".report-print-btn").attr("data-title");
         $(".report-print-btn").attr("data-title", title+" (" + sub_title + ")");
     });
 </script>
<div class="report-filter">
<table style="width:100%">
    <tr>
        <td><h3>Sales Report <span class="title_dsc"></span></h3> </td>
        <td>
           <form style="float:right" action="" class="form-inline" method="get">
                <label>Date</label> 
                <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control"> - 
                <input name="enddate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control">
                   <label>Store</label> 
                <select name="store" class="form-control"><option value="0">All Stores</option>
                    <?php 
                    $stores = \DB::select("select * from store");
                    ?>
                    
                    @foreach($stores as $store)
                    <option value="{{ $store->idstore}}">{{ $store->store_name }}</option>
                    @endforeach
                </select>
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
                <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Sales Report" class="btn btn-default report-print-btn">Print</button>
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
   
        <td  class="diff">{{ $item->item_sum }} </td>
        <?php $bill_totals +=  $item->item_sum; ?>
        <td>{{ isset($bill_pay[$item->idbills]->cash) ? $bill_pay[$item->idbills]->cash : 0 }} </td>
        <td>{{ isset($bill_pay[$item->idbills]->bank_card) ? $bill_pay[$item->idbills]->bank_card : 0 }} </td>
  
        <td>{{ isset( $bill_pay[$item->idbills]->check_amount) ? $bill_pay[$item->idbills]->check_amount : 0}} </td>
        <td>{{ $item->paid }} </td>
    </tr>
    <?php $visa += isset($bill_pay[$item->idbills]->bank_card) ? $bill_pay[$item->idbills]->bank_card : 0; 
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