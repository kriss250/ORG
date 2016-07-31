@extends("Frontdesk::MasterIframe")
@section("contents")

<?php
$res = \Kris\Frontdesk\Reservation::find($_GET['id']);
$subtotal = 0;
?>
<script type="text/javascript">
    initSelectBoxes();
    $("body").removeClass("ifr-body");
</script>

<button onclick="window.print()" style="margin:5px auto;display:block" class="btn btn-primary">
    <i class="fa fa-print"></i>Print
</button>
<div class="print-document">
    @include("Frontdesk::print-header")

    <div class="row">
        <div class="col-xs-7">
            <p>Guest names : {{$res->guest->firstname}} {{$res->guest->lastname}}</p>
            <p>Phone : {{$res->guest->phone}}</p>
            <p>Company : {{$res->company != null ? $res->company->name : ""}}</p>
            <hr style="max-width:350px" />
            <p>Arrival : {{ ( new \Carbon\Carbon($res->checkin))->format("d/m/Y")}}</p>
            <p>Departure : {{( new \Carbon\Carbon($res->checkout))->format("d/m/Y")}}</p>
        </div>

        <div class="col-xs-5 text-right">
            <p>Room : {{$res->room->room_number}}</p>
        </div>
    </div>
    <br />

    <table class="table table-condensed bill-table">
        <thead>
            <tr>
                <th>Date</th>
                <th width="40%">Description</th>
                <th width="12%" class="text-right">Qty / Nights</th>
                <th class="text-right">U.Price</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <?php $min = 12; $total = 0; $items = \Kris\Frontdesk\Bill::getBillItems($_GET['id']);  
              $min = count($items) > $min ? count($items) : $min;
        ?>


        @for($i=0;$i<$min;$i++)

        @if(isset($items{$i}))
        <tr>
            <td class="text-left">{{( new \Carbon\Carbon($items{$i}->date))->format("d/m/Y") }}</td>
            <td class="text-left">{{$items{$i}->motif}}</td>
            <td class="text-right">{{$items{$i}->qty}}</td>
            <td class="text-right">{{$items{$i}->unit_price}}</td>
            <?php $subtotal = $items{$i}->unit_price*$items{$i}->qty ?>
            <td class="text-right">{{number_format($subtotal)}}</td>
        </tr>
        @else 
        <tr style="text-indent:-99999px">
            <td class="text-left">.</td>
            <td class="text-left"></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
        </tr>
        @endif
        <?php $total += $subtotal;?>

        @endfor

        <?php $VAT =$total*18/118; ?>
    </table>

    <div style="margin-top:-15px;" class="row">
        <div class="col-xs-4">

        </div>
        <div class="col-xs-8 bill-summary text-right">

            <p>SUBTOTAL(TAX EXCLUSIVE) <b>{{number_format($total-$VAT)}}</b></p>
            <p>VAT <b>{{number_format($VAT)}}</b></p>
            <span>TAX INCLUSIVE <b>{{number_format($total)}}</b></span>
            <span>
                PAID
                <b>{{number_format($res->paid_amount)}}</b>
            </span>
            <hr />
            <span>
                DUE
                <b>{{number_format($total-$res->paid_amount)}}</b>
            </span>
        </div>
    </div>
    <div style="margin-top:15px;" class="row">
        <div class="col-xs-6 text-left">
            <p><b>Customer Signature</b></p>
            <br />
        </div>

        <div class="col-xs-6 text-right">
            <p>
                <b>Receptionist Signature</b><br />
                {{\Kris\Frontdesk\User::me()->firstname}} {{\Kris\Frontdesk\User::me()->lastname}}
            </p>

            <br />
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
    @if(session('msg'))
        alert("{{session('msg')}}");
    @elseif(session('errors'))
        error('{{session("errors")->first()}}');
    @endif
    });

</script>
@stop