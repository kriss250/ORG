@extends('Backoffice.Master')

@section("contents")

@if (session('status'))
    <div class="alert alert-success">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ session('status') }}
    </div>
@endif


@if (session('error'))
    <div class="alert alert-danger">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ session('error') }}
    </div>
@endif

<?php 
$days="";
  $day_sales = "";
  ?>
@foreach($weeksales as $wk) 
  <?php 
  
        $day_sales .= $wk->total.",";
        $days .= "'".$wk->day."',";
   ?>
@endforeach
<script type="text/javascript">
    $(function () {
    $('#container').highcharts({
        chart: {
            type: 'areaspline'
        },
        title: {
            text: 'Daily Bar and Restaurant sales'
        },
        legend: {
            layout: 'vertical',
            align: 'left',
            verticalAlign: 'top',
            x: 150,
            y: 100,
            floating: true,
            borderWidth: 1,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
        },
        xAxis: {
            categories: [
                {!! trim($days,',') !!}
            ],
            plotBands: [{ // visualize the weekend
                from: 4.5,
                to: 6.5,
                color: 'rgba(68, 170, 213, .2)'
            }]
        },
        yAxis: {
            title: {
                text: 'Rwf'
            }
        },
        tooltip: {
            shared: true,
            valueSuffix: ' Rwf'
        },
        credits: {
            enabled: false
        },
        plotOptions: {
            areaspline: {
                fillOpacity: 0.5
            }
        },
        series: [{
            name: 'Last Week',
            data: [2020000,1988500,2261600,3098400,1029000]
        }, {
            name: 'This week',
            data: [{{trim($day_sales,',')}}]
        }]
    });
});
</script>
<div class="col-md-9" style="padding-left:0">

    <?php $sum = 0;$credit=0;$room_posts=0; ?>

    @foreach ($bills as $bill)
    <?php $sum +=$bill->total; ?>
   
        @if($bill->status ==\ORG\Bill::CREDIT)
            <?php $credit +=$bill->total;  ?>
        @endif
   
        @if($bill->status ==\ORG\Bill::ASSIGNED)
            <?php $room_posts +=$bill->total; ?>
        @endif

    @endforeach

    <div class="color-box pink">
        
        <span class="icon"><i class="fa fa-cart-arrow-down"></i></span>
        <div class="text">
               <h4>POS Debts</h4>
        <p>{{ number_format($credit) }} Rwf</p><p>Daily Debts</p>
        </div>
     
    </div>

     <div class="color-box light-green">
        
        <span class="icon"><i class="fa fa-cart-arrow-down"></i></span>
        <div class="text">
               <h4>Room Posts</h4>
      
        <p>Sales {{ number_format($room_posts) }} </p>
        <p>Bills posted to rooms</p>
       
        </div>
     
    </div>

    <div class="color-box blue">
        
        <span class="icon"><i class="fa fa-cart-arrow-down"></i></span>
        <div class="text">
               <h4>POS Sales</h4>
        <p>{{ number_format($sum) }}</p>
         <p>Room posts ,Credits</p>
        </div>
     
    </div>



    <div class="clearfix"></div>
    <br />

<div class="home-chart" style="min-width: 310px; height: 350px; margin: 0 auto" id="container"></div>

    <br />
<p>RECENT PAYMENTS <em>POS</em></p>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID#</th>
                <th>Amount</th>
                <th>Mode</th>
                <th>Date</th>
                <th>Time</th>
            </tr>
        </thead>

@if(isset($payments) && count($payments)>0) 
@foreach($payments as $payment)
<?php $tsp = strtotime($payment->date); ?>
        <tr>
            <td>{{ $payment->idpayments }}</td>
            

             @if($payment->bank_card>0)
             <td>{{ $payment->bank_card }}</td>
            <td>Card</td>
            @else 
            <td>{{ $payment->cash }}</td>
            <td>Cash</td>
            @endif

            <td>{{ date("d/m/Y",$tsp) }}</td>
            <td>{{ date("H:i:s",$tsp) }}</td>
        </tr>

@endforeach

@else 
<tr>
    <td colspan="5">There are no payments yet</td>
</tr>
@endif
    </table>

</div>

<div class="col-md-3">
    <div class="widget" style="border:1px solid rgb(227, 207, 218)">
    <div class="widget-text">
        <table style="width:100%;line-height:1.1">
            <tr>
            @foreach($exchangerates as $rate)
                <td style="padding:5px">
                <i style="font-size:22px" class="fa fa-{{ strtolower($rate->currency) }}"></i> 
                </td>

                <td style="padding:5px">
                <span class="text-danger">{{ floor($rate->buying) }} (B)</span> <br>  <span class="text-success">{{ floor($rate->selling) }} (S)</span>
                </td>
            @endforeach
            </tr>
        </table>
        <p style="font-size:11px;color:#ccc;margin-top:5px;margin-bottom:-5px" class="text-center">{{ $exchangerates[0]->date }}</p>
       
    </div>
    </div>

    <div class="widget" style="border: 1px solid rgb(163, 149, 173)">
        <p class="widget-title" style="background:#7C5590">Cash Balance</p>
            <table style="margin-bottom:0" class="table-responsive  table-striped table">
            @foreach($cashbooks as $book)
                <tr>
                    <td>{{ $book->cashbook_name }}</td><td> {{ number_format($book->balance) }}</td>
                </tr>

            @endforeach

            </table>
    </div>
     @if(\Auth::user()->level >= 9)
    <div class="widget green">
        <p class="widget-title">User Activities</p>
        <div class="widget-text">
            <ul class="activities-list">
            @foreach($logs as $log)
               <?php  $tsp = strtotime($log->date); ?>

                <li><p>{{$log->username}} : {{ $log->action }}</p><i class="fa fa-clock-o"></i> {{ date("H:i",$tsp) }}
                    <span>{{ date("d",$tsp) }}</span>
                </li>
            @endforeach 

            </ul>
        </div>
    </div>
    @endif

</div>


<div class="clearfix"></div>
@stop