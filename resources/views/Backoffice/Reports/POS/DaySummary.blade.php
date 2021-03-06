@extends("Backoffice.Master")
 <?php  $visa = 0; $cash = 0; $check_amount=0; $credit = 0;$paidbills_total =0; $room_post = 0;$paidbill_total=0; $bill_totals = 0;$total= 0; $total_paid=0;$total_credit=0;$total_roompost= 0;

 $_visa = 0;
 $_cash = 0;

 ?>


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
        <td><h3>Day Summary <span class="title_dsc"></span></h3> </td>
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

                <input type="submit" class="btn btn-success btn-sm" value="Go">
                <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['enddate']) ? $_GET['enddate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Day Summary" class="btn btn-default report-print-btn">Print</button>
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

<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th class="text-center" colspan="9">SALES (PAID)</th>
    </tr>
        <tr>
            <th>Order No</th>
            <th>Customer</th>
            <th>Room</th>
            <th class="diff">Amount</th>
            <th>Cash</th>
            <th>Visa</th>
            <th>Check</th>
             <th>Paid</th>
            <th>Balance</th>
        </tr>
    </thead>

    @foreach($sales['bills'] as $sale)
    <?php $_v = 0; $_c = 0; $_ck = 0; ?>
       <tr>
            <td>{{ $sale->idbills }}</td>
            <td>{{ $sale->customer }}</td>
            <td>{{ $sale->room }}</td>
            <td class="diff">{{ number_format($sale->bill_total) }}</td>
            <?php
             $percent = $sale->bill_total/100;
             $_v = (100 * $sales['pays'][$sale->idbills]->bank_card / $sale->total_amount) * $percent;
             $_c = (100 * $sales['pays'][$sale->idbills]->cash / $sale->total_amount) * $percent;
             $_ck = 100 * $sales['pays'][$sale->idbills]->check_amount / $sale->total_amount * $percent;
             ?>

            <td>{{ number_format($_c)  }}</td>

            <td>{{ number_format($_v)   }} </td>

            <td>{{ number_format($_ck) }}</td>

            <td>{{ number_format($sale->paid) }}</td>
            <?php $balance = $sale->bill_total-$sale->paid; ?>
            <td>{{ $balance > 0 ? number_format($balance) : "-" }}</td>
        </tr>
        <?php

            $visa +=$_v;
            $cash += $_c;

            $check_amount +=$_ck;
            $paidbill_total +=$sale->paid;

            $bill_totals += $sale->bill_total;
            $paidbills_total +=$sale->bill_total;
        ?>
    @endforeach

    <tr>
            <td colspan="3">TOTAL</td>
            <td class="diff">{{ number_format($bill_totals) }}</td>
            <td>{{ number_format($cash) }}</td>
            <td> {{ number_format($visa) }}</td>
            <td> {{ number_format($check_amount) }}</td>
            <td>{{ isset($_GET['store']) && $_GET['store'] > 0  ? '-'  : number_format($cash+$visa+$check_amount) }}</td>
    </tr>
</table>

<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th class="text-center" colspan="5">ROOM POSTS</th>
    </tr>
        <tr>
            <th>Order No</th>
            <th>Customer</th>
            <th>Room</th>
            <th>Amount</th>
            <th>Paid</th>
        </tr>
    </thead>

@foreach($room  as $_room)
    <tr>
            <td>{{ $_room->idbills }}</td>
            <td>{{ $_room->customer }}</td>
            <td>{{ $_room->room}}</td>
            <td>{{ number_format($_room->bill_total) }}</td>
            <td>{{ number_format($_room->paid)}}</td>
        </tr>

        <?php $total_roompost += $_room->bill_total; $total_paid +=  $_room->paid; ?>

@endforeach
<tr>
    <td colspan="3">TOTAL</td>
    <td>{{ number_format($total_roompost) }}</td>
    <td>{{ $total_paid }} </td>
</tr>

</table>


<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th class="text-center" colspan="5">CREDITS</th>
    </tr>
        <tr>
            <th>Order No</th>
            <th>Customer</th>
            <th>Room</th>
            <th>Amount</th>
            <th>Paid</th>
        </tr>
    </thead>

@foreach($credits  as $_credit)
    <tr>
            <td>{{ $_credit->idbills }}</td>
            <td>{{ $_credit->customer }}</td>
            <td>{{ $_credit->room}}</td>
            <td>{{ number_format($_credit->bill_total) }}</td>
            <td>{{ number_format($_credit->paid)}}</td>
        </tr>

        <?php $total_credit += $_credit->bill_total; $total_paid +=  $_credit->paid; ?>

@endforeach
<tr>
    <td colspan="3">TOTAL</td>
    <td>{{ number_format($total_credit) }}</td>
    <td>{{ $total_paid }} </td>
</tr>

<table class="table table-stripped table-bordered">
<thead>
    <tr>
        <th>TOTAL SALES</th>
        <th>DEBTS(CREDIT)</th>
        <th>ROOM POSTS</th>
        <th>CASH</th>
        <th>VISA</th>
        <th>PAID</th>
    </tr>
</thead>
    <tr>
        <td>{{ number_format($paidbills_total+$total_credit+$total_roompost) }}</td>
        <td>{{number_format($total_credit) }}</td>
        <td>{{ number_format($total_roompost) }}</td>
        <td>{{ $_cash > 0 ? number_format($_cash) : number_format($cash) }} </td>
        <td> {{ $_visa > 0 ? number_format($_visa) : number_format($visa) }}</td>
        <td>{{ isset($_GET['store']) && $_GET['store'] > 0  ? '-'  : number_format($paidbill_total) }}</td>
    </tr>
</table>
</table>
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
