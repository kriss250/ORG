@extends("Backoffice.Master")

@section("contents")
<div class="page-contents">
     
<div class="report-filter">
<table style="width:100%">
    <tr>
        <td><h3>Cashier Bills</h3> </td>
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
                 <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Cashier Bills" class="btn btn-default report-print-btn">Print</button>
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
@if(isset($data))

    <table class="table table-bordered table-stripped">
        <thead>
            <tr>
                <th>Cashier</th>
                <th>Waiter</th>
                <th>Room</th>
                <th>Order ID</th>
                <th>Amount</th>
                <th>Date</th>
            </tr>
        </thead>
        @foreach($data as $cashier)
            <tr>
                <td>{{ $cashier->username}}</td>
                <td>{{ $cashier->waiter}}</td>
                <td>{{ $cashier->room}}</td>
                <td>{{ $cashier->idbills}}</td>
                <td>{{ $cashier->bill_total}}</td>
                <td>{{ $cashier->date }}</td>
            </tr>
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


</div>

@stop