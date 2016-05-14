
@extends("Backoffice.Master")

@section("contents")
<div class="page-contents">

<div class="report-filter">
<table style="width:100%">
    <tr>
        <td><h3>Credit Report</h3> </td>
        <td>
            <form style="float:right" action="" class="form-inline" method="get">
                <label>Date</label> 
                <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control"> - 
                <input name="enddate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control">
                <input type="submit" class="btn btn-success btn-sm" value="Go">
                 <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Credit Report" class="btn btn-default report-print-btn">Print</button>
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

@if(isset($bills))
    
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