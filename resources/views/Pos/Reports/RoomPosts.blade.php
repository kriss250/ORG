@extends("Pos.master")


@section("contents")


<div class="report-filter">
<table style="width:100%">
    <tr>
        <td><h3>Room Post Report </h3> </td>
        <td>
          <form style="float:right" action="" class="form-inline" method="get">
                   <label>Date</label>
               <input class="input-sm form-control date-picker" type="text" value="{{ \ORG\Dates::$RESTODATE }}" name="startdate" /> -
                <input class="input-sm form-control date-picker" type="text" value="{{ \ORG\Dates::$RESTODATE }}" name="enddate" />
                
                
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
                 <button type="button" data-dates="{{ isset($_GET['date']) ? $_GET['date'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Room Posts" class="btn btn-default report-print-btn">Print</button>
           </form> 
        </td>
    </tr>
</table>

    <p class="report-desc"><i class="fa fa-information-circle"></i>Full report of bills posted to rooms</p>

</div>

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
    <?php $tt2=0; ?>

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
        @if(isset($room->customer))
             <td rowspan="{{ $items_count+1}}">{{ $room->customer }}</td>
        @endif
        <td rowspan="{{ $items_count+1}}">{{ $room->room}}</td>
        <td style="padding: 0"  colspan="4"> </td>
        <td rowspan="{{$items_count+1}}">{{ $room_sub_total }}</td>
    </tr>

   {!! $tr !!}
    @endforeach





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
    
@stop