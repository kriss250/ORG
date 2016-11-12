@extends("Frontdesk::MasterIframe")

@section("contents")
<button onclick="window.print()" style="margin:5px auto;display:block" class="btn btn-primary">
    <i class="fa fa-print"></i>Print
</button>
<div class="print-document">
    @include("Frontdesk::receipt-print-header")
                <br />
   <h2 class="text-center">RECEIPT</h2>
    <br />
    <?php
    $payment->reservation->load("guest");
    ?>
    <div style="font-size:12px">
        <h4>Receipt No : {{$payment->id_folio}}/{{date("y")}}</h4>
        Customer : {{$payment->reservation->guest->firstname}} {{$payment->reservation->guest->lastname}} <br />
        Room : {{$payment->reservation->room->room_number}} <br />
        Payment Currency : {{$payment->currency->alias}} <br />
        Date of Payment : {{$payment->date}} <br />
    </div>
    <hr />
    <table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th>Account Ref#</th>
                <th>Amount Paid</th>
                <th>Payment Method</th>
                <th>Amount Paid({{$payment->currency->alias}})</th>
                <th>Value in Local Currency</th>
            </tr>

        
        </thead>


        <tr style="font-size:15px">
            <td>{{$payment->reservation->idreservation}}</td>
            <td>{{number_format($payment->credit)}}</td>
            <td>{{$payment->mode->method_name}}</td>
            <td>{{number_format($payment->original_amount)}}</td>
            <td>{{number_format($payment->credit)}}</td>
        </tr>

    </table>
    <p>Amount in words :  {{(new NumberFormatter("en", NumberFormatter::SPELLOUT))->format($payment->original_amount)}} ({{$payment->currency->alias}})</p>
    <br />
    <p>Note: {{$payment->motif}}</p>
    

    <hr />
    <br />
    <b>Receptionist</b>
    <p> {{$payment->user->lastname}} {{$payment->user->firstname}} </p>
    <br />
    Signature ____________________________________

</div>

@stop
