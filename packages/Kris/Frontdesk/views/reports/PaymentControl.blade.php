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
$laundry=0;
$bar=0;
$due=0;
$paid=0;
$balance=0;
$firstRow = false;
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
    $rows  .= "<tr>";
    $rows .= "<td>{$res->room_number}</td>";
    $rows .= "<td>{$res->type_name}</td>";
    $rows .= "<td>{$res->guest }</td>";
    if($firstRow){
        $rows .= "<td $span>".\App\FX::Date($res->checkin)."</td>" ;
        $rows .=  "<td $span>".\App\FX::Date($res->checkout)."</td>";
        $rows .=   "<td $span>".number_format($res->night_rate)."</td>" ;
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
         $rows .=   "<td $span>".$res->payer."</td>" ;
         $balance +=($res->due_amount-$res->balance_amount);
         $due +=$res->due_amount;
         $paid +=$res->balance_amount;
     }
    $rows .=" </tr>";
    $spanx = $span;
    $i++;
}
$rows .= "<tfoot>";
$rows .= "<tr><th colspan='5'>TOTAL</th><th>".number_format($tariff)."</th><th>".number_format($resto)."</th><th>".number_format($bar)."</th><th>".number_format($laundry)."</th><th>".number_format($due)."</th><th>".number_format($paid)."</th><th>".number_format($balance)."</th><th></th>";
$rows .="</tr>";
?>
@include("Frontdesk::reports.report-filter")

<div class="print-document">

    @include("Frontdesk::reports.report-print-header")
    <p class="report-title">Payments Report</p>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Room</th>
                <th>Room Type</th>
                <th>Guest</th>
                <th>Checkin</th>
                <th>Checkout</th>
                <th>Acc.</th>
                <th>Resto</th>
                <th>Bar</th>
                <th>Laundry</th>
                <th>Total Due</th>
                <th>Paid</th>
                <th>Bal.</th>
                <th>Payer</th>
            </tr>
        </thead>

        {!!$rows !!}
    </table>


    @include("Frontdesk::reports.report-footer")

</div>

@stop

