@extends("Backoffice.Master")

@section("contents")

<div class="page-contents">

<div class="report-filter">
<table style="width:100%">
    <tr>
        <td><h3>POS Orders Report</h3> </td>
        <td>
           <form style="float:right" action="" class="form-inline" method="get">
                <label>Date</label> 
                <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control"> - 
                <input name="enddate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control">
                <label>Cashier</label> 
                <select name="waiter" class="form-control">
 				<option value="0">All</option>
                    <?php
                    $waiters = \App\Waiter::all();
                    ?>

                    @if(isset($waiters))
                        @foreach($waiters as $waiter)
                            <option value="{{ $waiter->idwaiter }}">{{$waiter->waiter_name}}</option>
                        @endforeach
                    @endif
                </select>
                <input type="submit" class="btn btn-success btn-sm" value="Go">
                 <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="POS Orders Report" class="btn btn-default report-print-btn">Print</button>
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
   
    <style>
        .deleted-order td {
            text-decoration: line-through !important;
        }
    </style>
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
            
        </tr>
    </thead>
    {!!$tr!!}

    <tfoot>
        <tr>
            <th colspan="8">TOTAL</th>
            <th>{{ number_format($total) }}</th>

            <th></th>
            <th colspan="1"></th>
        </tr>
    </tfoot>
</table>

</div>

@stop