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
$total_paid =0;

$i=0;
$firstRow = false;
$totals = ["acco"=>0,"services"=>0,"totals"=>0,"credits"=>0,"paid"=>0];
foreach($data as $res) {


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

    $days = (strtotime($res->checked_out) - strtotime($res->checked_in)) / 60 / 60 / 24;


    $total_paid += $span ==1 && strlen($res->idreservation) > 0 ? $res->paid_amount : 0;

    $rows  .= "<tr title='Due : {$res->due_amount} - Diff:".($res->services+$res->acco)."'>";
    $rows .= "<td>".($i+1)."</td>";
    $rows .= "<td>{$res->room_number}</td>";

    $rows .= "<td>{$res->guest}</td>";

    if($firstRow){
        $rows .="<td $span>".(strlen($res->company) > 0 ? $res->company : "WALKIN")."</td>";
    }

    $rows .="<td>".$res->checked_in."</td>";
    $rows .="<td>".$res->checked_out."</td>";

    $rows .= "<td>{$days}</td>";

    $rows .="<td>".number_format($res->night_rate)."</td>";

    //$res->acco = $days * $res->night_rate; //fix

    $rows .="<td>".number_format($res->acco)."</td>";
    $rows .="<td>".number_format($res->services)."</td>";

    $totals["acco"] +=$res->acco;
    $totals["services"] += $res->services;

    $totals["totals"] += $res->services+$res->acco;

    $rows .="<td>".number_format($res->services+$res->acco)."</td>";

    if($firstRow){
        $totals['paid'] += $res->paid_amount;

        $rows .= "<td $span>".number_format($res->paid_amount)."</td>";//paid
        if($res->pay_by_credit=="1"){
            $totals["credits"] += $res->due_amount-$res->paid_amount;
            $rows .=  "<td $span>".number_format($res->due_amount-$res->paid_amount)."</td>";//credit
        }else {
            $rows .=  "<td $span>".number_format(0)."</td>";//credit

        }
        $rows .= "<td $span>".$res->payer."</td>";
    }
    $rows .=" </tr>";


    $spanx = $span;
    $i++;
}

$rows .="
    <tfoot>
        <tr>
             <th colspan='8'>Total</th>
            <th>".number_format($totals["acco"])."</th>
            <th>".number_format($totals["services"])."</th>
            <th>".number_format($totals["totals"])."</th>
            <th>".number_format($totals["paid"])."</th>
            <th>".number_format($totals["credits"])."</th>
            <th></th>
        </tr>
    </tfoot>
";

//$rows .="<tfoot>
//<tr style='font-weight:bold'>


//<td colspan='8'>TOTAL</td>
//<td>".number_format($total_tariff)."</td>
//<td>".number_format($total_due)."</td><td></td></tr>
//</tfoot>";
?>
<div class="page-contents">

<div class="report-filter">
<table style="width:100%">
    <tr>
        <td><h3>Departure Report </h3> </td>
        <td>
          <form style="float:right" action="" class="form-inline" method="get">
                <label>Date</label> 
                <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control"> - 
                <input name="enddate" type="text" value="{{\ORG\Dates::$RESTODATE }}" class="date-picker form-control">

                        @if(isset($_GET['import']))
                        <input type="hidden" name="import" value="" />
                        @endif

                <input type="submit" class="btn btn-success btn-sm" value="Go">
                 <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Departure Report" class="btn btn-default report-print-btn">Print</button>
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

    <table class="table table-bordered table-condensed table-striped">
        <thead>
               <tr>
                <th>#</th>
                <th>Room</th>
                <th>Guest</th>
                <th>Company</th>
                <th>Checkin</th>
                <th>Checkout</th>
                <th>Days</th>
                <th>Tariff</th>
                <th>Acco.</th>
                <th>Services</th>
                <th>Total</th>
                <th>Paid</th>
                <th>Credit</th>
                <th>Payer</th>
            </tr>
        </thead>

        <?php vprintf($rows,$spans); ?>
 
    </table>



<div class="text-center print-footer">
       <table style="margin-bottom:85px;width:100%;" class="table">
           <tr>
               <td>
                   RECEPTIONIST
               </td>

               <td>
                   CONTROLLER
               </td>

               <td>
                   C.S.M.M
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

