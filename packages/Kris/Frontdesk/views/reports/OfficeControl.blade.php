@extends("Frontdesk::MasterIframe")
@section("contents")
<?php
$rows = "";
static $spanx =1;
$spans = [];
$span = 1;
$i=0;
$tariff=0;
$resto=0;
$bar=0;
$due=0;
$paid=0;
$balance=0;
$firstRow =false;
$otherServices = 0;
foreach($data as $res) {
    $span= "";
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
    $rows  .= "<tr title='Bal D: {$res->due_amount} Res ID : {$res->idreservation}'>";
    if($res->shifted > 0 && $res->gsize > 1)
    {
        if($firstRow)
        {
            $rows .= "<td $span>".($i+1)."</td>";
        }
    }else {
        $rows .= "<td>".($i+1)."</td>";
    }
    $rows .= "<td>".$res->room_number.($res->shifted > 0 ? " (Shifted) " :"" )."</td>";
    $rows .= "<td>{$res->type_name}</td>";
    $rows .= "<td>{$res->guest}</td>";
    if($firstRow){
        $rows .= "<td $span>".(strlen($res->Company) > 0 ? $res->Company : "WALKIN")."</td>" ;
    }

    $rows .= "<td>{$res->package_name}</td>";
    $rows .= "<td>".\App\FX::Date($res->checked_in)."</td>";
    $rows .=  "<td>".\App\FX::Date($res->checked_out)."</td>";
    if($firstRow){
        $rows .=   "<td $span>".number_format($res->night_rate)."</td>";
        $tariff += $res->night_rate;
    }
    $bar += $res->bar;
    $resto +=$res->resto;
    $otherServices += $res->other;
    $rows .="<td>".number_format($res->resto)."</td>";
    $rows .="<td>".number_format($res->bar)."</td>";
    $rows .="<td>".number_format($res->other)."</td>";
    $total = ($res->acco+$res->charges);
    if($firstRow){
        $rows .=   "<td $span>".number_format($total)."</td>" ;
        $rows .=   "<td $span>".number_format($res->payments)."</td>" ;
        $rows .=   "<td $span>".number_format($total-$res->payments)."</td>" ;
        $balance +=($total-$res->payments);
        $due += $total;
        $paid += $res->payments;
    }
    $rows .=" </tr>";
    $spanx = $span;
    $i++;
}
$rows .= "<tfoot>";
$rows .= "<tr><th colspan='7'>TOTAL</th><th>".number_format($tariff)."</th><th>".number_format($resto)."</th><th>".number_format($bar)."</th><th>".number_format($otherServices)."</th><th>".number_format($due)."</th><th>".number_format($paid)."</th><th>".number_format($balance)."</th>";
$rows .="</tr>";
?>
@include("Frontdesk::reports.report-filter")

<div class="print-document">
    @include("Frontdesk::reports.report-print-header")

    <p class="report-title">Front office Control</p>
    <table class="table table-bordered table-striped">
        <thead>
               <tr>
                <th>#</th>
                <th>Room</th>
                 <th>Room Type</th>
                 <th>Guest Names</th>
                 <th>Company</th>
                   <th>PKG</th>
                 <th>Checkin</th>
                 <th>Checkout</th>
                 <th>Tariff</th>
                <th>Resto</th>
                <th>Bar</th>
                <th>Services</th>
                 <th>Total Due</th>
                <th>Paid</th>
                <th>Balance</th>
            </tr>
        </thead>

        {!!$rows !!} 
    </table>

    <br />

    @include("Frontdesk::reports.report-footer")

</div>

@stop

