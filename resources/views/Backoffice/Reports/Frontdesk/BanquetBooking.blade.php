@extends(isset($_GET['import']) ? "ORGFrontdesk.Reports.Master" : "Backoffice.Master")

@section("contents")

<style>
    .table td p {
        border-bottom: 1px solid;
        padding-bottom: 5px;
        margin-bottom: 0;
    }


    

        .table td p:last-child {
            border-bottom: none;
        }
</style>

<div class="page-contents">

    <div class="report-filter">
        <table style="width:100%">
            <tr>
                <td>
                    <h3>Banquet Booking </h3>
                </td>
                <td>
                    <form style="float:right" action="" class="form-inline" method="get">
                        <label>Date</label>
                        <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control" />
                        -
                        <input name="enddate" type="text" value="{{\ORG\Dates::$RESTODATE }}" class="date-picker form-control" />
                        @if(isset($_GET['import']))
                        <input type="hidden" name="import" value="" />
                        @endif

                        <input type="submit" class="btn btn-success btn-sm" value="Go" />
                        <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Banquet Booking Report" class="btn btn-default report-print-btn">Print</button>
                    </form>
                </td>
            </tr>

            <tr>
                <td>
                    <p class="text-danger">
                        <b>Date : {{isset($_GET['startdate']) && isset($_GET['enddate'])  ?  \App\FX::Date($_GET['startdate'])." - ".\App\FX::Date($_GET['enddate']) : \App\FX::Date(\ORG\Dates::$RESTODATE) }}</b>
                    </p>
                </td>
            </tr>

        </table>
    </div>

    <table class="table table-bordered table-condensed table-striped">
        <thead>
            <?php  $tsp = strtotime($range[0]); $rows = ""; $tds = "";  ?>
            <tr>
                <th>Date</th>
                @foreach($banquets as $banquet)
                    <th>{{$banquet->banquet_name}}</th>

                <?php $tds .= "<td class='b_$banquet->idbanquet'></td>"; ?>
                @endforeach
            </tr>
        </thead>

        <?php

        $data = ["date"=>"","orders"=>""];

        while($range[0] <= $range[1]){

            $rows .="<tr class='d_$tsp'>";
            $rows .="<td>".date("d/m/Y", $tsp)."</td>";
            $rows .= $tds;
            $rows .="</tr>";

            $tsp= strtotime('+1 days', strtotime($range[0])); $range[0] = date("Y-m-d",$tsp);
        }

       
        ?>

        {!!$rows!!}
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

<?php
   foreach($orders as $order){
    $tsp = strtotime($order->arv) ;
    $str = "$('.d_{$tsp} .b_{$order->banquet_id}').prepend('<p>'+ '{$order->guest} (Pax:{$order->pax})</p>' );";

    echo "<script>$(document).ready(function(){ {$str} })</script>";
   }

?>
@stop

