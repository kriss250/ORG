@extends("Frontdesk::MasterIframe")


@section("contents")

<?php
$cash=0;
$check=0;
$bank=0;
$cc = 0;
$i=1;
$totals = ["cash"=>0,"bank"=>0,"cc"=>0,"check"=>0];
?>
@include("Frontdesk::reports.report-filter")
<div class="print-document">

    @include("Frontdesk::reports.report-print-header")
    <p class="report-title">Payments Report</p>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>ID</th>
                <th>Guest</th>
                <th>Company</th>
                <th>Room</th>
                <th>Cash</th>
                <th>CC</th>
                <th>Check</th>
                <th>Bank T.</th>
                <th>Comment</th>
                <th>User</th>
                <th>Date</th>
            </tr>
        </thead>
        
        @foreach ($data as $pay)
        <?php

        if(!isset($cr_totals[$pay->cur]))
        {
            $cr_totals[$pay->cur] = 0;
        }

        switch(strtolower($pay->method_name)){
            case "cash" :
                $cash = $pay->credit;
                $cc = 0;
                $bank = 0;
                $check = 0;

                $cash_o=["amt"=>$pay->original_amount,"c"=>$pay->cur];
                $check_o=["amt"=>0,"c"=>null];
                $bank_o=["amt"=>0,"c"=>null];
                $cc_o = ["amt"=>0,"c"=>null];

                $cr_totals[$pay->cur] += $pay->original_amount;
                $totals["cash"] +=$pay->credit;
                break;
            case "credit card" :
                $cash = 0;
                $cc = $pay->credit;
                $bank = 0;
                $check = 0;

                $cash_o=["amt"=>0,"c"=>null];
                $check_o=["amt"=>0,"c"=>null];
                $bank_o=["amt"=>0,"c"=>null];
                $cr_totals[$pay->cur] += $pay->original_amount;
                $cc_o = ["amt"=>$pay->original_amount,"c"=>$pay->cur];

                $totals["cc"] +=$pay->credit;
                break;
            case "check":
                $cash = 0;
                $cc = 0;
                $bank = 0;
                $check = $pay->credit;

                $cash_o=["amt"=>0,"c"=>null];
                $check_o=["amt"=>$pay->original_amount,"c"=>$pay->cur];
                $bank_o=["amt"=>0,"c"=>null];
                $cc_o = ["amt"=>0,"c"=>null];

                $totals["check"] +=$pay->credit;
                $cr_totals[$pay->cur] += $pay->original_amount;
                break;
            case "bank op" :
                $cash = 0;
                $cc = 0;
                $bank = $pay->credit;
                $check = 0;

                $cash_o=["amt"=>0,"c"=>null];
                $check_o=["amt"=>0,"c"=>null];
                $bank_o=["amt"=>$pay->original_amount,"c"=>$pay->cur];
                $cc_o = ["amt"=>0,"c"=>null];
                $totals["bank"] +=$pay->credit;
                $cr_totals[$pay->cur] += $pay->original_amount;
                break;
        }


        ?>
            <tr>
                <td>{{    $i}}</td>
                <td>
                    <a href="#" onclick="javascript:window.open('{{action("CustomersController@printBill",$pay->idreservation)}}?type=standard','','width=920,height=620',this);return;">{{$pay->idreservation}}</a>
                </td>
                <td>{{$pay->guest}}</td>
                <td>{{$pay->company}}</td>
                <td>{{$pay->room_number}}</td>
                <td>{{$cash}}{{$cash_o["amt"]!=$cash ? " (".$cash_o["c"].$cash_o["amt"].")":""}}</td>
                <td>{{$cc}} {{$cc_o["amt"]!=$cc ? " (".$cc_o["c"].$cc_o["amt"].")":""}}</td>
                <td>{{$check}} {{$check_o["amt"]!=$check ? " (".$check_o["c"].$check_o["amt"].")":""}}</td>
                <td>{{$bank}} {{$bank_o["amt"]!=$bank ? " (".$bank_o["c"].$bank_o["amt"].")":""}}</td>
                <td>{{$pay->comment}}</td>
                <td>{{$pay->username}}</td>
                <td>{{$pay->date}}</td>
            </tr>
        <?php $i++; ?>
        @endforeach
   
        <tfoot>
            <tr>
                <th colspan="5">Total</th>
                <th>{{number_format($totals['cash'])}}</th>
                <th>{{number_format($totals['cc'])}}</th>
                <th>{{number_format($totals['check'])}}</th>
                <th>{{number_format($totals['bank'])}}</th>
                <th class="text-red text-right"><b style="color:darkred">Grand Total:</b></th>
                <th class="text-right" colspan="2">
                    <b style="font-size:14px;color:#000">{{number_format($totals['cash']+$totals['cc']+$totals['bank']+$totals['check'])}}</b>
                </th>
            </tr>
        </tfoot>
    </table>

    Currencies
    <table class="table table-bordered table-condensed">
        <tr>
            @foreach($cr_totals as $key=>$val)
            <th><b>{{$key}} {{number_format($val)}}</b></th>
           @endforeach
        </tr>
    </table>


    @include("Frontdesk::reports.report-footer")

</div>

@stop

