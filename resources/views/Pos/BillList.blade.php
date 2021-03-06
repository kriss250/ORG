@extends("Pos.master")

@section("contents")

<div class="row">
    <div class="col-md-9">
        <h2 style="margin-bottom:-2px">POS Bills</h2>
    </div>

    @if(\Auth::user()->level > 8)
    <div class="col-md-3">
        <form class="form-inline" action="" method="get">
            <label>Date</label>
            <div class="form-group">
                
                <input class="input-sm form-control date-picker" type="text" value="{{ \ORG\Dates::$RESTODATE }}" name="startdate" />
                <input class="btn btn-sm btn-success group-addon" type="submit" value="Go" />
            </div>
        </form>
    </div>
    @endif

</div>

<br />

<script type="text/javascript">
    $(document).ready(function(){
        $(".cancel-pay-btn").click(function(e){
            e.preventDefault();

            if(!confirm("Are you sure you want to reset this bill's payment ?"))
            {
                return false;
            }

            
            var id  = $(this).attr("data-id");
            var btn = $(this);

            $.get("{{ action('BillsController@deleteBillPayments') }}?id="+id+"&ignore="+$(btn).attr("data-ignore"),function(data){
                if(data=="1")
                {
                    $(btn).children('.fa').removeClass('fa-check-circle').addClass('fa-question-circle');
                    $(btn).parent().parent().find(".amt-col").html("0");
                }else {
                    ualert.error("Unable to remove payment from this bill, Please contact your system administrator");
                }
            });
        })
    })
</script>
<p class="page_info">
    <i class="fa fa-info-circle"></i>
     Use this table to find bills and their status , you can also reset the bill's payment by clicking on the green button on the last column in the bill's row.</p>

<table class="table table-bordered table-striped bills-table">
 
    <?php
    $totals= array("bill"=>"","cash"=>"","check"=>"","card"=>"");
    $sub_rows ="";
    $tr= "";

    foreach($bills as $bill) {

       $zi = count($bill->items)+1; $sub_row= "";

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

        $discount = 0;
        $discount =  $bill->is_fixed_discount ? $bill->discount : ($bill->discount/100)*$bill->bill_total;
        $totals['bill'] += $bill->bill_total - $discount;

        $tr .= "<tr>
                 <td rowspan='$zi'>$bill->idbills ".($bill->last_updated_by>0 && $bill->last_updated_by != $bill->user_id ? "<b style='color:red;font-size:16px'>*</b>" : "" )."</td>
                  <td  rowspan='$zi'>$bill->customer </td>
                  <td  rowspan='$zi'>$bill->username</td>
                  <td  rowspan='$zi'>$bill->waiter_name</td>

                  <td class='no-cell' colspan='4'></td>
                  <td  rowspan='$zi'>".($discount > 0 ? "<span class='lnt'>{$bill->bill_total} </span>". ($bill->bill_total - $discount) : $bill->bill_total )."</td>
                  <td  rowspan='$zi'>$bill->status_name</td>
                  <td  class='amt-col' rowspan='$zi'>$bill->cash </td>
                  <td class='amt-col' rowspan='$zi'>$bill->card</td>
                  <td class='amt-col' rowspan='$zi'>$bill->check_amount</td>
                  <td rowspan='$zi'>".\App\FX::Time($bill->date)."</td>
                  <td rowspan='$zi'>
                     <button class='cancel-pay-btn' data-ignore='".(($bill->status==\ORG\Bill::OFFTARIFF) ? "1" : "0")."' data-id='$bill->idbills'><i class='fa fa-".(($bill->status==\ORG\Bill::SUSPENDED) ? 'question' : 'check')."-circle'></i></button>
                  <button onclick='printBill($bill->idbills);' style='background:none;border:none;width:100%;text-align:center'><i class='fa fa-print'></i></button>
                  </td>
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
        <th>Action</th>
    </tr>
</thead>
    {!!$tr!!}


    <tfoot>
        <tr>
            <th colspan="8">TOTAL</th>
            <th>{{ $totals['bill']}}</th>
            <th></th>
            <th>{{ $totals['cash']}}</th>
            <th>{{ $totals['card']}}</th>
            <th>{{ $totals['check']}}</th>
            <th colspan="2"></th>
        </tr>
    </tfoot>
</table>
@stop