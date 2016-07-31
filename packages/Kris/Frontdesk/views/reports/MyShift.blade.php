@extends("Frontdesk::MasterIframe")

@section("contents")

@include("Frontdesk::reports.report-filter")

<div class="print-document">

    @include("Frontdesk::reports.report-print-header")

    <p class="report-title">Shift Report - {{\FO::me()->username}}</p>
    <?php $cash = 0;$bank = 0;$check = 0;$cc = 0; $credit = 0; $refund=0; ?>
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
            switch($payment->paymethod)
            {
                case 1:
                    $cash += $payment->pay;
                    break;
                case 2:
                    $cc += $payment->pay;
                    break;

                case 3:
                    $check += $payment->pay;
                    break;
                case 4:
                    $bank += $payment->pay;
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

            switch($sale->pay_mode)
            {
                case 1:
                    $cash += $sale->amount;
                    break;
                case 2:
                    $cc += $sale->amount;
                    break;

                case 3:
                    $check += $sale->amount;
                    break;
                case 4:
                    $bank += $sale->amount;
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
    </table>

    @include("Frontdesk::reports.report-footer")

</div>

@stop

