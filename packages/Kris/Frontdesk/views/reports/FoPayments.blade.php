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
        switch(strtolower($pay->method_name)){
            case "cash" : 
                $cash = $pay->credit;
                $cc = 0;
                $bank = 0;
                $check = 0;
                $totals["cash"] +=$pay->credit;
                break;
            case "credit card" :
                $cash = 0;
                $cc = $pay->credit;
                $bank = 0;
                $check = 0;
                $totals["cc"] +=$pay->credit;
                break;
            case "check":
                $cash = 0;
                $cc = 0;
                $bank = 0;
                $check = $pay->credit;
                $totals["check"] +=$pay->credit;
                break;
            case "bank op" : 
                $cash = 0;
                $cc = 0;
                $bank = $pay->credit;
                $check = 0;
                $totals["bank"] +=$pay->credit;
                break;
        }


        ?>
            <tr>
                <td>{{$i}}</td>
                <td>{{$pay->guest}}</td>
                <td>{{$pay->company}}</td>
                <td>{{$pay->room_number}}</td>
                <td>{{$cash}}</td>
                <td>{{$cc}}</td>
                <td>{{$check}}</td>
                <td>{{$bank}}</td>
                <td>{{$pay->comment}}</td>
                <td>{{$pay->username}}</td>
                <td>{{$pay->date}}</td>
            </tr>
        <?php $i++; ?>
        @endforeach
   
        <tfoot>
            <tr>
                <th colspan="4">Total</th>
                <th>{{number_format($totals['cash'])}}</th>
                <th>{{number_format($totals['cc'])}}</th>
                <th>{{number_format($totals['check'])}}</th>
                <th>{{number_format($totals['bank'])}}</th>
                <th class="text-red text-right"><b style="color:darkred">Grand Total:</b></th>
                <th class="text-right" colspan="2">
                    <b style="font-size:13px">{{number_format($totals['cash']+$totals['cc']+$totals['bank']+$totals['check'])}}</b>
                </th>
            </tr>
        </tfoot>
    </table>


    @include("Frontdesk::reports.report-footer")

</div>

@stop

