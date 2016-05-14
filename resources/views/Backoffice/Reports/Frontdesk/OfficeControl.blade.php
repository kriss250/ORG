@extends(isset($_GET['import']) ? "ORGFrontdesk.Reports.Master" : "Backoffice.Master")

@section("contents")
<?php
$rows = "";
static $spanx =1;
$spans = [];
$span = 1;
$i=0;
$tariff=0;
$resto=0;
$laundry=0;
$bar=0;
$due=0;
$paid=0;
$balance=0;
$firstRow =false;
foreach($data as $res) {

    #region IS IT THE FIRT ROW
    $span = $res->gsize > 1 ? "rowspan='$res->gsize'" : "";
    if($i==0)
    {
        //first Row of the table
        $firstRow  = true;
    }else if($data[$i-1]->gsize != $res->gsize || $res->gsize  < 2){
        //First Row of the group or row with span =1
        $firstRow = true;
    }else {
        $firstRow = false;
    }

    #endregion

    $rows  .= "<tr>";
    $rows .= "<td>{$res->room_number}</td>";
    $rows .= "<td>{$res->type_name}</td>";
    $rows .= "<td>{$res->guest }</td>";

    if($firstRow){
        $rows .= "<td $span>{$res->Company}</td>" ;
        $rows .= "<td $span>".\App\FX::Date($res->checkin)."</td>";
        $rows .=  "<td $span>".\App\FX::Date($res->checkout)."</td>";
        $rows .=   "<td $span>".number_format($res->night_rate)."</td>";
    }

    $tariff += $res->night_rate;
    $laundry += $res->laundry;
    $bar += $res->bar;
    $resto +=$res->resto;

    $rows .="<td>".number_format($res->resto)."</td>";
    $rows .="<td>".number_format($res->bar)."</td>";
    $rows .="<td>".number_format($res->laundry)."</td>";

  
    if($firstRow){
        $rows .=   "<td $span>".number_format($res->due_amount)."</td>" ;
        $rows .=   "<td $span>".number_format($res->balance_amount)."</td>" ;
        $rows .=   "<td $span>".number_format($res->due_amount-$res->balance_amount)."</td>" ;

        $balance +=($res->due_amount-$res->balance_amount);
        $due +=$res->due_amount;
        $paid +=$res->balance_amount;
    }

    $rows .=" </tr>";
    $spanx = $span;
    $i++;
}

$rows .= "<tfoot>";
$rows .= "<tr><th colspan='6'>TOTAL</th><th>".number_format($tariff)."</th><th>".number_format($resto)."</th><th>".number_format($bar)."</th><th>".number_format($laundry)."</th><th>".number_format($due)."</th><th>".number_format($paid)."</th><th>".number_format($balance)."</th>";
$rows .="</tr>";
?>

<div class="page-contents">

<div class="report-filter">
<table style="width:100%">
    <tr>
        <td><h3>Frontoffice Control </h3> </td>
        <td>
          <form style="float:right" action="" class="form-inline" method="get">
                <label>Date</label> 
                <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control"> - 
                <input name="enddate" type="text" value="{{\ORG\Dates::$RESTODATE }}" class="date-picker form-control">

                        @if(isset($_GET['import']))
                        <input type="hidden" name="import" value="" />
                        @endif

                <input type="submit" class="btn btn-success btn-sm" value="Go">
                 <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Frontoffice Control" class="btn btn-default report-print-btn">Print</button>
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

    <table class="table table-bordered">
        <thead>
               <tr>
                <th>Room</th>
                 <th>Room Type</th>
                 <th>Guest</th>
                 <th>Company</th>
                 <th>Checkin</th>
                 <th>Checkout</th>
                 <th>Tariff</th>
                <th>Resto</th>
                <th>Bar</th>
                <th>Laundry</th>
                 <th>Total Due</th>
                <th>Paid</th>
                <th>Bal.</th>
            </tr>
        </thead>

        {!! $rows !!} 
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

