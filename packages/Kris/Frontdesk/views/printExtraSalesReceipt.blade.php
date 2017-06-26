@extends("Frontdesk::MasterIframe")

@section("contents")
<button onclick="window.print()" style="margin:5px auto;display:block" class="btn btn-primary">
    <i class="fa fa-print"></i>Print
</button>
<div class="print-document">
    @include("Frontdesk::receipt-print-header")
                <br />
   <h2 class="text-center">RECEIPT (Extra Sales)</h2>
    <br />

    <div style="font-size:12px">
        <h4>Receipt No : {{$payment->idmisc_sales}}/{{date("y")}}</h4>
        Customer : {{$payment->guest}} <br />
        Service : {{$payment->service}} <br />
        Payment Currency : {{$payment->alias}} <br />
        Date of Payment : {{$payment->date}} <br />
    </div>
    <hr />
    <table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th>Payment Method</th>
                <th>Amount Paid({{$payment->alias}})</th>
                <th>Value in Local Currency</th>
            </tr>

        
        </thead>


        <tr style="font-size:15px">
            <td>{{$payment->method_name}}</td>
            <td>{{number_format($payment->original_amount,1)}}</td>
            <td>{{number_format($payment->amount,1)}}</td>
        </tr>

    </table>
    <p>Amount in words :  {{(new NumberFormatter("en", NumberFormatter::SPELLOUT))->format($payment->original_amount)}} ({{$payment->alias}})</p>
    <br />
    <p>Note: {{$payment->description}}</p>
    

    <hr />
    <br />
    <b>Receptionist</b>
    <p> {{$payment->username}}</p>
    <br />
    Signature ____________________________________

</div>

@stop
