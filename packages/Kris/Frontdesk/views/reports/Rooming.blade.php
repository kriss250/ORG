@extends("Frontdesk::MasterIframe")

@section("contents")
<style type="text/css">


</style>
<?php
$rows = "";
static $spanx =1;
$spans = [];
$span = 1;
$i=0;
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
    $rows  .= "<td>".($i+1)."</td>";
    $rows .= "<td>".$res->room_number.($res->shifted > 0 ? "(Shifted)" : "")."</td>";
    $rows .= "<td>{$res->type_name}</td>";
    $rows .= "<td>{$res->guest }</td>";
    $rows .= "<td>{$res->id_doc}</td>";
    if($firstRow){
        $rows .=  "<td $span>{$res->Company}</td>";
        $rows .=  "<td $span>".\App\FX::Date($res->checkin)."</td>" ;
        $rows .=  "<td $span>".\App\FX::Date($res->checkout)."</td>";
    }
    $rows .="<td>".$res->country."</td>";
    $rows .="<td>".$res->phone."</td>";
    $rows .=" </tr>";
    $i++;
}
?>
@include("Frontdesk::reports.report-filter")
<div class="print-document">
    @include("Frontdesk::reports.report-print-header")
    <p class="report-title">Police Report</p>
    <table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th>#</th>
                <th>Room</th>
                <th>Room Type</th>
                <th>Guest</th>
                <th>ID/Pas No.</th>
                <th>Company</th>
                <th>Checkin</th>
                <th>Checkout</th>
                <th>Country</th>
                <th>Contact</th>
            </tr>
        </thead>
        {!!$rows !!}


    </table>



    @include("Frontdesk::reports.report-footer")

</div>

@stop

