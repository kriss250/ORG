@extends("Pos.master")

@section("contents")

<div class="row">
    <div class="col-md-9">
        <h2 style="margin-bottom:-2px">POS Orders</h2>
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
<style>

    .cancel-order-btn {
        border:none;
        color:#ff0000;
        background:none;
        font-size:16px;
    }

    .deleted-order td{
        text-decoration: line-through !important;
    }
</style>
<br />

<script type="text/javascript">
    $(document).ready(function(){
        $(".cancel-order-btn").click(function(e){
            e.preventDefault();

            if(!confirm("Are you sure you want to delete this order?"))
            {
                return false;
            }

            
            var id  = $(this).attr("data-id");
            var btn = $(this);

            $.get("{{ action('OrdersController@delete') }}?id="+id,function(data){
                if(data=="1")
                {
                    location.reload();
                }else {
                    ualert.error("Unable to delete the order, Please contact your system administrator");
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
    $sub_rows ="";
    $tr= "";
    $total = 0;
    foreach($bills as $bill) {

       $zi = count($bill->items)+1; $sub_row= "";
      $total += $bill->deleted == 1 ? 0 : $bill->total;

        foreach($bill->items as $item){
            $sub_rows .='<tr>
                <td>'.$item->product_name.'</td>
                <td>'.$item->qty.'</td>
                <td>'.$item->unit_price.'</td>
                <td>'.number_format($item->unit_price*$item->qty).'</td></tr>
                ';
        }

        $tr .= "<tr ".($bill->deleted > 0 ? 'class="deleted-order"':'').">
                 <td rowspan='$zi'>{$bill->idorders}</td>
                  <td  rowspan='$zi'>{$bill->store_name}</td>
                  <td  rowspan='$zi'>{$bill->bill_id}</td>
                  <td  rowspan='$zi'>{$bill->waiter_name} {$bill->lastname}</td>

                  <td class='no-cell' colspan='4'></td>
                  <td  rowspan='$zi'>".number_format($bill->total)."</td>
                  <td  rowspan='$zi'>-</td>

                  <td rowspan='$zi'>".\App\FX::Time($bill->date)."</td>
                  <td rowspan='$zi'>
                     <button class='cancel-order-btn'data-id='{$bill->idorders}'><i class='fa fa-".(($bill->bill_id<0 || $bill->bill_id == null) ? 'trash' : 'check')."'></i></button>
                  </td>
            </tr>".$sub_rows;

            $sub_rows= "";


}
    ?>
<thead>
    <tr>
        <th>Order</th> 
        <th>Store</th>
        <th>Bill</th>
        <th>Waiter</th>
        <th class="text-center" colspan="4">Order Items</th>
        <th>Total</th>
        <th>Status</th>
        <th>Time</th>
        <th>Action</th>
    </tr>
</thead>
    {!!$tr!!}

    <tfoot>
        <tr>
            <th colspan="8">TOTAL</th>
            <th>{{ number_format($total) }}</th>
       
            <th></th>
            <th colspan="2"></th>
        </tr>
    </tfoot>
</table>
@stop