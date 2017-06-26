@extends("Frontdesk::MasterIframe")

@section("contents")
@include("Frontdesk::reports.report-filter")

<div class="print-document">
    @include("Frontdesk::reports.report-print-header")
    <p class="report-title">Extra Sales</p>
    <table class="table table-condensed table-bordered">
        <thead>
            <tr>
                <th>Guest</th>
                <th>Service</th>
                <th>Receipt</th>
                <th>Paid</th>
                <th>Credit</th>
                <th>Pay. Method</th>
                <th>User</th>
                <th>Date</th>
            </tr>
        </thead>
        <?php $credit  = 0 ; $paid=0; ?>
        @foreach($sales as $sale)
        <?php
         if(!isset($cr_totals[$sale->alias]))
        {
           $cr_totals[$sale->alias] = 0;
        }
        ?>

        <tr>
            <td class="text-left">{{$sale->guest}}</td>
            <td>{{$sale->service}}</td>
            <td>{{$sale->receipt}}</td>
            <td>{{$sale->is_credit == "0" ? $sale->amount : ""}}</td>
            <td>{{$sale->is_credit == "1" ? $sale->amount : ""}}</td>
            <td>{{$sale->method_name}}</td>
            <td>{{$sale->username}}</td>
            <td>{{$sale->date}}</td>

            <?php
                if($sale->is_credit==0)
                {
                    $paid += $sale->amount;
                    $cr_totals[$sale->alias] += $sale->amount;
                }else {
                    $credit += $sale->amount;
                }

            ?>
        </tr>
        @endforeach
        
        <tfoot>
            <tr>
                <td colspan="3">TOTAL</td>
                <td>{{ number_format($paid)}}</td>
                <td>{{number_format($credit)}}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    Currencies
    <table class="table table-bordered table-condensed">
        <tr>
            @foreach($cr_totals as $key=>$val)
            <th>
                <b>{{$key}} {{number_format($val)}}</b>
            </th>
            @endforeach
        </tr>
    </table></div>

@stop
