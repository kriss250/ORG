@extends("Pos.master")
 <?php  $visa = 0; $cash = 0; $check_amount=0; $credit = 0;$paidbills_total =0; $room_post = 0;$paidbill_total=0; $bill_totals = 0;$total= 0; $total_paid=0;$total_credit=0;$total_roompost= 0;

 $_visa = 0;
 $_cash = 0;

 ?>
@section("contents")

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
        <td><h3>Sales Report V2</h3> </td>
        <td>
           <form style="float:right" action="" class="form-inline" method="get">
               <label>Date</label>
               <input class="input-sm form-control date-picker" type="text" value="{{ \ORG\Dates::$RESTODATE }}" name="startdate" /> -
                <input class="input-sm form-control date-picker" type="text" value="{{ \ORG\Dates::$RESTODATE }}" name="enddate" />

                 <label>Store</label>
                <select name="store" class="form-control input-sm"><option value="0">All Stores</option>
                    <?php
                    $stores = \DB::select("select * from store");
                    ?>

                    @foreach($stores as $store)
                    <option value="{{ $store->idstore}}">{{ $store->store_name }}</option>
                    @endforeach
                </select>


                <input type="submit" class="btn btn-success btn-sm" value="Go">
                <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['enddate']) ? $_GET['enddate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Sales Report" class="btn btn-default report-print-btn">Print</button>
           </form>

        </td>
    </tr>
</table>

    <p class="report-desc"><i class="fa fa-information"></i>Summarized report of sold and paid bills, as well as room post and credits</p>
</div>
<h4>ALL BILLS <i></i></h4>
<table class="table table-bordered table-striped bills-table table-condensed">

<?php
    $totals= array("bill"=>"0","cash"=>"0","check"=>"0","card"=>"0");
    $sub_rows ="";
    $tr= "";

    foreach($bills as $bill) {

       $zi = count($bill->items)+1; $sub_row= "";

       $billGT = 0;
       $_cash_percent = 0;
       $_card_percent = 0;
       $_check_percent = 0;

        foreach($bill->items as $item){
            $sub_rows .='<tr>
                <td>'.$item->product_name.'</td>
                <td>'.$item->qty.'</td>
                <td>'.$item->unit_price.'</td>
                <td>'.number_format($item->unit_price*$item->qty).'</td></tr>
                ';

                if($bill->status != \ORG\Bill::OFFTARIFF)
                {
                  $billGT += $item->qty * $item->unit_price;
                }
        }

        try{
            $_cash_percent = (($bill->cash * 100) / $bill->bill_total)/100;
            $_card_percent = (($bill->card * 100) / $bill->bill_total)/100;
            $_check_percent = (($bill->check_amount * 100) / $bill->bill_total)/100;
        }catch(\Exception $ex){
            
        }

        if($zi>1){
            $tr .= "<tr".($bill->status == \ORG\Bill::SUSPENDED ? " class='text-danger' ":"").">
                 <td rowspan='$zi'>$bill->idbills ".($bill->status == \ORG\Bill::SUSPENDED ? " <i class='fa fa-question-circle'></i>":"")." ".($bill->last_updated_by>0 && $bill->last_updated_by != $bill->user_id ? "<b style='color:red;font-size:16px'>*</b>" : "" )."</td>
                  <td  rowspan='$zi'>$bill->customer </td>
                  <td  rowspan='$zi'>$bill->username</td>
                  <td  rowspan='$zi'>$bill->waiter_name</td>

                  <td class='no-cell' colspan='4'></td>
                  <td  rowspan='$zi'>".number_format($billGT)."</td>
                  <td  class='amt-col' rowspan='$zi'>".number_format($_cash_percent*$billGT)."</td>
                  <td class='amt-col' rowspan='$zi'>".number_format($_card_percent*$billGT)."</td>
                  <td class='amt-col' rowspan='$zi'>".number_format($_check_percent*$billGT)."</td>
                  <td rowspan='$zi'>".\App\FX::Time($bill->date)."</td>

            </tr>".$sub_rows;
        }
            $totals['cash'] +=$_cash_percent*$billGT;
            $totals['card'] +=$_card_percent*$billGT;
            $totals['check']+=$_check_percent*$billGT;
            $totals['bill'] +=$billGT;
            $sub_rows= "";

}
?>
    <tr style="font-weight:bold">
        <td rowspan="2">Order</td>
        <td rowspan="2">Customer</td>
        <td rowspan="2">Cashier</td>
        <td rowspan="2">Waiter</td>
        <td class="text-center" colspan="4">Bill Items</td>
        <td rowspan="2">Total</td>
        <td rowspan="2">Cash</td>
        <td rowspan="2">Card</td>
        <td rowspan="2">Check</td>
        <td rowspan="2">Time</td>
    </tr>
    <tr>
        <td>Item</td>
        <td>Qty</td>
        <td>U.P</td>
        <td>T.P</td>
    </tr>
    {!!$tr!!}

        <tr>
            <th colspan="8">TOTAL</th>
            <th>{{ number_format($totals['bill']) }}</th>
            <th>{{ number_format($totals['cash'],0) }}</th>
            <th>{{ number_format($totals['card'],0) }}</th>
            <th>{{ number_format($totals['check'],0) }}</th>
            <th colspan="2"></th>
        </tr>
</table>
<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th class="text-center" colspan="6">ROOM POSTS <b></b></th>
    </tr>
        <tr>
            <th>Order No</th>
            <th>Customer</th>
            <th>Company</th>
            <th>Room</th>
            <th>Amount</th>
            <th>Paid</th>
        </tr>
    </thead>

@foreach($room  as $_room)
    <tr>
            <td>{{ $_room->idbills }}</td>
            <td>{{ $_room->customer }}</td>
            <td>{{$_room->company}}</td>
            <td>{{ $_room->room}}</td>
            <td>{{ number_format($_room->bill_total) }}</td>
            <td>{{ number_format($_room->paid)}}</td>
        </tr>

        <?php $total_roompost += $_room->bill_total; $total_paid +=  $_room->paid; ?>

@endforeach
<tr>
    <td colspan="4">TOTAL</td>
    <td>{{ number_format($total_roompost) }}</td>
    <td>{{ $total_paid }} </td>
</tr>

</table>


<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th class="text-center" colspan="5">CREDITS <b></b></th>
    </tr>
        <tr>
            <th>Order No</th>
            <th>Customer</th>
            <th>Amount</th>
            <th>Paid</th>
        </tr>
    </thead>

@foreach($credits  as $_credit)
    <tr>
            <td>{{ $_credit->idbills }}</td>
            <td>{{ $_credit->customer }}</td>
            <td>{{ number_format($_credit->bill_total) }}</td>
            <td>{{ number_format($_credit->paid)}}</td>
        </tr>

        <?php $total_credit += $_credit->bill_total; $total_paid +=  $_credit->paid; ?>

@endforeach
<tr>
    <td colspan="2">TOTAL</td>
    <td>{{ number_format($total_credit) }}</td>
    <td>{{ $total_paid }} </td>
</tr>
</table>
<table class="table table-stripped table-bordered">
<thead>
    <tr>
        <th>TOTAL SALES</th>
        <th>CREDIT</th>
        <th>ROOM POSTS</th>
        <th>CASH</th>
        <th>VISA</th>
        <th>PAID</th>
    </tr>
</thead>
    <tr>
        <td>{{ number_format($totals['bill']) }}</td>
        <td>{{number_format($total_credit) }}</td>
        <td>{{ number_format($total_roompost) }}</td>
        <td>{{ number_format($totals['cash'])}} </td>
        <td> {{ number_format($totals['card'])}}</td>
        <td>{{ number_format($totals['cash']+$totals['card']) }}</td>
    </tr>
</table>

<div class="text-center print-footer">
       <table style="margin-bottom:85px;width:100%;" class="table">
           <tr>
               <td>
                   CASHIER
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


    </div>
@stop
