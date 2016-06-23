@if(isset($info))

<h3>{{$info->guest}}</h3>
<p style="margin-bottom:0"><i class="fa fa-compass"></i> {{$info->country}}</p>
<p><i class="fa fa-phone"></i> {{$info->phone}}</p>
<div style="padding:15px;">
    <div style="background: rgb(246, 246, 246) none repeat scroll 0% 0%;padding: 13px 10px;line-height: 0.8;border: 1px solid rgb(230, 230, 230);border-radius: 6px;" class="row">
        <div class="col-sm-7">
            <p>Company : {{ $info->company}}</p>
            <p>Night Rate  : {{ number_format($info->night_rate)}}</p>
            <p>Room : {{$info->room_number}}</p>
            <p>Checked in By : {{$info->username}}</p>
        </div>

        <div class="col-sm-5">
            <p>Checkin : {{ \App\FX::Date($info->checkin)}}</p>
            <p>Checkout : {{\App\FX::Date($info->checkout)}}</p>
        </div>
    </div>
</div>
<h5>Charges</h5>

<table style="text-align:left" class="table table-condensed table-bordered">
    <thead>
        <tr>
            <th>Description</th>
            <th>Amount</th>
            <th>User</th>
            <th>Date</th>
        </tr>
    </thead>
    <?php  $chargesx = 0;?>
    @if($charges != null)
        @foreach($charges as $charge)
            <tr>
                <td>{{$charge->motif}}</td>
                <td>{{number_format($charge->amount)}}</td>
                <td>{{$charge->user}}</td>
                <td>{{$charge->date}}</td>
            </tr>

    <?php $chargesx += $charge->amount;?>
        @endforeach
    @endif
</table>
<h5>Payments</h5>

<table style="text-align:left" class="table table-condensed table-bordered">
    <thead>
        <tr>
            <th>Description</th>
            <th>Amount</th>
            <th>Mode of Pay.</th>
            <th>User</th>
            <th>Date</th>
        </tr>
    </thead>

    @if($payments != null)
        @foreach($payments as $pay)
            <tr>
                <td>{{$pay->motif}}</td>
                <td>{{number_format($pay->credit)}}</td>
                <td>{{$pay->method_name}}</td>
                <td>{{$pay->username}}</td>
                <td>{{$pay->date}}</td>
            </tr>
        @endforeach
    @endif
</table>

Summary
<table class="table table-condensed table-bordered">
    <thead>
        <tr>
            <th>Accomodation</th>
            <th>Services</th>
            <th>Total Due</th>
            <th>Amount Paid</th>
            <th>Balance</th>
        </tr>
    </thead>

    <tr>
        <td>{{number_format($info->acco)}}</td>
        <td>{{number_format($chargesx)}}</td>
        <td>{{number_format($info->due_amount)}}</td>
        <td>{{number_format($info->balance_amount)}}</td>
        <td>{{number_format($info->due_amount-$info->balance_amount)}}</td>
    </tr>
</table>

@endif