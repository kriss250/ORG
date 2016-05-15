
@extends(isset($_GET['import']) ? "ORGFrontdesk.Reports.Master" : "Backoffice.Master")

@section("contents")
<style type="text/css">


</style>
<?php
$rows = "";
static $spanx =1;
$spans = [];
$span = 1;

$total_tariff=0;
$total_due =0;
$firstRow= false;

$i =0;
foreach($data as $res) {


    $total_tariff += $res->night_rate;


    $rows  .= "<tr>";

    #region IS IT THE FIRT ROW
    $span = $res->gsize > 1 ? "rowspan='$res->gsize'" : "";
    if($i==0)
    {
        //first Row of the table
        $firstRow  = true;
    }else if( $data[$i-1]->idreservation != $res->idreservation ){
        //First Row of the group or row with span =1
        $firstRow = true;
    }else {
        $firstRow = false;
    }

    #endregion

    $total_due += $firstRow && strlen($res->idreservation) > 0 ? $res->due_amount : 0;
    $rows .= "<td>".($i+1)."</td>";
    $rows .= "<td>{$res->room_number}</td>";
    $rows .= "<td>{$res->type_name}</td>";
    $rows .= "<td>{$res->guest}</td>";

    if($firstRow)
    {
        $rows .= "<td $span>{$res->Company}</td>" ;
        $rows .= "<td $span>".\App\FX::Date($res->checkin)."</td>";
        $rows .= "<td $span>".\App\FX::Date($res->checkout)."</td>";
    }

    $rows .="<td>".number_format($res->night_rate)."</td>";

    if($firstRow)
    {
        $rows .="<td $span>".number_format($res->due_amount)."</td>";
        $rows .= "<td $span>".$res->payer."</td>";
    }

    $rows .=" </tr>";

    $i++;
}


;
$rows .="<tfoot>
<tr style='font-weight:bold'>


<td colspan='7'>TOTAL</td>
<td>".number_format($total_tariff)."</td>
<td>".number_format($total_due)."</td><td></td></tr>
</tfoot>";
?>
<div class="page-contents">

<div class="report-filter">
<table style="width:100%">
    <tr>
        <td><h3>Room Sales Report </h3> </td>
        <td>
          <form style="float:right" action="{{URL::full()}}" class="form-inline" method="get">
                <label>Date</label> 
                <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control"> - 
                <input name="enddate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control">
               
                @if(isset($_GET['import']))
                    <input type="hidden" name="import" value="" />
                @endif
                <input type="submit" class="btn btn-success btn-sm" value="Go">
                 <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Room Sales Report" class="btn btn-default report-print-btn">Print</button>
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

    <table class="table table-bordered">
        <thead>
               <tr>
                   <th>#</th>
                <th>Room</th>
                 <th>Room Type</th>
                 <th>Guest</th>
                 <th>Company</th>
                 <th>Checkin</th>
                 <th>Checkout</th>
                 <th>Tariff</th>
                 <th>Due Amount</th>
                <th>Payer</th>
            </tr>
        </thead>
        <?php echo $rows; ?>
 
        
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

@stop

