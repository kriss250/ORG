@extends("Backoffice.Master")

@section("contents")
<div class="page-contents">

<div class="report-filter">
<table style="width:100%">
    <tr>
        <td><h3>Free Consumption Report</h3> </td>
        <td>
           <form style="float:right" action="" class="form-inline" method="get">
                <label>Date</label> 
                <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control"> - 
                <input name="enddate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control">
                <label>Cashier</label> 
                <select name="cashier" class="form-control">
 				<option value="0">All</option>
                    <?php
                        $cashiers = \DB::select("select id,username from users");
                    ?>

                    @if(isset($cashiers))
                        @foreach($cashiers as $cashier)
                            <option value="{{ $cashier->id }}">{{$cashier->username}}</option>
                        @endforeach
                    @endif
                </select>
                <input type="submit" class="btn btn-success btn-sm" value="Go">
                 <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Free Consumption Report" class="btn btn-default report-print-btn">Print</button>
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

<table class="table table-bordered table-striped bills-table">
 
<?php
    $totals= array("bill"=>"","cash"=>"","check"=>"","card"=>"");
    $sub_rows ="";
    $tr= "";
   
    foreach($bills as $bill) {
     
       $zi = count($bill->items)+1; $sub_row= ""; 
       $totals['bill'] += $bill->bill_total;
       $totals['cash'] += $bill->cash;
       $totals['card'] +=$bill->card;
       $totals['check'] += $bill->check_amount;

        foreach($bill->items as $item){
            $sub_rows .='<tr>
                <td>'.$item->product_name.'</td>
                <td>'.$item->qty.'</td>
                <td>'.$item->unit_price.'</td>
                <td>'.number_format($item->unit_price*$item->qty).'</td></tr>
                ';
        }

        $tr .= "<tr>
                 <td rowspan='$zi'>$bill->idbills ".($bill->last_updated_by>0 && $bill->last_updated_by != $bill->user_id ? "<b style='color:red;font-size:16px'>*</b>" : "" )."</td>
                  <td  rowspan='$zi'>$bill->customer </td>
                  <td  rowspan='$zi'>$bill->username</td>
                  <td  rowspan='$zi'>$bill->waiter_name</td>
              
                  <td class='no-cell' colspan='4'></td>
                  <td  rowspan='$zi'>$bill->bill_total</td>
                  <td  rowspan='$zi'>$bill->status_name</td>
                  <td  class='amt-col' rowspan='$zi'>$bill->cash </td>
                  <td class='amt-col' rowspan='$zi'>$bill->card</td>
                  <td class='amt-col' rowspan='$zi'>$bill->check_amount</td>
                  <td rowspan='$zi'>".\App\FX::DT($bill->date)."</td> 

            </tr>".$sub_rows;

            $sub_rows= "";

    
}
?>
<thead>
    <tr>
        <th>Order</th> 
        <th>Customer</th>
        <th>Cashier</th>
        <th>Waiter</th>
        <th class="text-center" colspan="4">Bill Items</th>
        <th>Total</th>
        <th>Status</th>
        <th>Cash</th>
        <th>Card</th>
        <th>Check</th>
        <th>Time</th>
    </tr>
</thead>
    {!!$tr!!}


    <tfoot>
        <tr>
            <th colspan="8">TOTAL</th>
            <?php try { ?>
            <th><?php number_format($totals['bill']) ?></th>
            <?php }catch(Exception $c) {} ?>
            <th></th>
            <th>{{ $totals['cash']}}</th>
            <th>{{ $totals['card']}}</th>
            <th>{{ $totals['check']}}</th>
            <th></th>
        </tr>
    </tfoot>
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