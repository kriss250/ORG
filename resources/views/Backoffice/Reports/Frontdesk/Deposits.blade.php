@extends(isset($_GET['import']) ? "ORGFrontdesk.Reports.Master" : "Backoffice.Master")

@section("contents")

<div class="page-contents">

<div class="report-filter">
<table style="width:100%">
    <tr>
        <td><h3>Deposits Report </h3> </td>
        <td>
          <form style="float:right" action="" class="form-inline" method="get">
                <label>Date</label> 
                <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control"> - 
                <input name="enddate" type="text" value="{{\ORG\Dates::$RESTODATE }}" class="date-picker form-control">
                        @if(isset($_GET['import']))
                        <input type="hidden" name="import" value="" />
                        @endif

                        <input type="submit" class="btn btn-success btn-sm" value="Go">
                 <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Deposits Report" class="btn btn-default report-print-btn">Print</button>
           </form> 
        </td>
    </tr>

     <tr>
      <td>
      <p class="text-danger"><b>Date : {{isset($_GET['startdate']) && isset($_GET['enddate'])  ?  \App\FX::Date($_GET['startdate'])." - ".\App\FX::Date($_GET['enddate']) : \App\FX::Date(\ORG\Dates::$RESTODATE) }}</b></p>
      </td>
    </tr>
    
</table>
</div>

<table class="table table-bordered table-striped table-condensed">
    <thead>
        <tr>
            <th>Particular</th>
            <th>RWF</th>
            <th>USD</th>
            <th>EURO</th>
            <th>VISA RWF</th>
            <th>VISA USD</th>
            <th>CHECK</th>
            <th>BANK OP</th>
        </tr>
    </thead>
    <?php $i=1;
    $totals = ["rwf"=>0,"usd"=>0,"euro"=>0,"visa_rwf"=>0,"visa_usd"=>0,"check_amount"=>0,"bank"=>0];
    ?>

    @foreach($data as $item)

    <tr>
        <td>{{$item->name}}</td>
        <td>{{number_format($item->rwf)}}</td>
        <td>{{number_format($item->usd)}}</td>
        <td>{{number_format($item->euro)}}</td>
        <td>{{number_format($item->visa_rwf)}}</td>
        <td>{{number_format($item->visa_usd)}}</td>
        <td>{{number_format($item->check_amount)}}</td>
        <td>{{number_format($item->bank)}}</td>
    </tr>
    <?php
        $i++;
        $totals["rwf"] += $item->rwf;
        $totals["usd"] += $item->usd;
        $totals["euro"] += $item->euro;
        $totals["visa_rwf"] += $item->visa_rwf;
        $totals["visa_usd"] += $item->visa_usd;
        $totals["check_amount"] += $item->check_amount;
        $totals["bank"] += $item->bank;

    ?>
    @endforeach
<tr>
    <th>TOTAL</th>
    <th>{{ number_format($totals["rwf"]) }} </th>
    <th>{{ number_format($totals["usd"]) }} </th>
    <th>{{ number_format($totals["euro"]) }} </th>
    <th>{{ number_format($totals["visa_rwf"]) }} </th>
    <th>{{ number_format($totals["visa_usd"]) }} </th>
    <th>{{ number_format($totals["check_amount"]) }} </th>
    <th>{{ number_format($totals["bank"]) }} </th>
</tr>
</table>

<div class="text-center print-footer">
       <table style="margin-bottom:85px;width:100%;" class="table">
           <tr>
               <td>
                   Receptionist
               </td>

               <td>
                   CASHIER
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

