@extends("Frontdesk::MasterIframe")

@section("contents")

@include("Frontdesk::reports.report-filter")

<div class="print-document">

    @include("Frontdesk::reports.report-print-header")

    <p class="report-title">Shift Report - {{\FO::me()->username}}</p>
    <?php 
    $cash = 0;$bank = 0;$check = 0;$cc = 0; $credit = 0; $refund=0;
          $userTotal = ["cash"=>0,"cc"=>0,"check"=>0,"bank"=>0,"refund"=>0,"credit"=>0];
    ?>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>DP</th>
                <th>Cash</th>
                <th>CC</th>
                <th>Check</th>
                <th>Bank Op</th>
                <th>Credit</th>
                <th>Refund</th>
            </tr>
        </thead>
        <tr>
            <td>Room</td>
            @foreach($payments as $payment)
            <?php
            $refund += $payment->refund;
            $userTotal["refund"] += $refund;
            switch($payment->paymethod)
            {
                case 1:
                    $cash += $payment->pay;
                    $userTotal["cash"] += $cash;
                    break;
                case 2:
                    $cc += $payment->pay;
                    $userTotal["cc"] += $cc;
                    break;

                case 3:
                    $check += $payment->pay;
                    $userTotal["check"] += $check;
                    break;
                case 4:
                    $bank += $payment->pay;
                    $userTotal["bank"] += $bank;
                    break;
            }
            ?>
            @endforeach

            <td>{{$cash}}</td>
            <td>{{$cc}}</td>
            <td>{{$check}}</td>
            <td>{{$bank}}</td>
            <td>-</td>
            <td>{{$refund}}</td>

           
        </tr>
        <?php $cash = 0;$bank = 0;$check = 0;$cc = 0; $credit = 0; $refund=0; ?>
        @foreach($sales as $sale)
        <?php
            $credit = $sale->is_credit ? $sale->amount : 0;
            $userTotal["credit"] +=$credit;
            switch($sale->pay_mode)
            {
                case 1:
                    $cash += $sale->amount;
                    $userTotal["cash"] += $cash;
                    break;
                case 2:
                    $cc += $sale->amount;
                    $userTotal["cc"] += $cc;
                    break;

                case 3:
                    $check += $sale->amount;
                    $userTotal["check"] +=$check;
                    break;
                case 4:
                    $bank += $sale->amount;
                    $userTotal["bank"] += $bank;
                    break;
            }
        ?>
            @endforeach
        
      
        <tr>
            <td>Extra Sales</td>
            <td>{{$cash}}</td>
            <td>{{$cc}}</td>
            <td>{{$check}}</td>
            <td>{{$bank}}</td>
            <td>{{$credit}}</td>
            <td>-</td>
        </tr>

        <tr class="text-bold">
            <td>TOTAL</td>
            <td>{{number_format($userTotal['cash'])}}</td>
            <td>{{number_format($userTotal['cc'])}}</td>
            <td>{{number_format($userTotal['check'])}}</td>
            <td>{{number_format($userTotal['bank'])}}</td>
            <td>{{number_format($userTotal['credit'])}}</td>
            <td>{{number_format($userTotal['refund'])}}</td>
        </tr>
    </table>

    @include("Frontdesk::reports.report-footer")

</div>

@stop

