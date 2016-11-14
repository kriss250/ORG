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
  $week1 = "";
  $week2 = "";
  $week1_days = "";
  $i=1;
  foreach ($weeksales[0] as $w)
  {
        $days .= "'".$w->day."',";
        $week1 .= $w->total.",";
  }
  foreach ($weeksales[1] as $w)
  {
        $days .= "'".$w->day."',";
        $week2 .= $w->total.",";
  }
?>

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
                {!!trim($days,',') !!}
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
            data: [{{trim($week1,',')}}]
        }, {
            name: 'This week',
            data: [{{trim($week2,',')}}]
        }]
    });

    $.ajax({
        type:"get",
        url:"{{action("BackofficeController@OccupiedRooms")}}",
        success:function(data)
        {
            data = JSON.parse(data);


            $.each(data,function(i,x){
                var itemDiv = $('<div class="col-md-6">');
                var itemTb = $('<table class="room-table table table-condensed">');

                var itemRow = $("<tr>");
                var col1Td = $('<td class="col1">');
                var Td = $("<td>");

                //FIRST ROW
                $(itemRow).append($(col1Td).html("<b>"+x.room_number+"</b>"));
                $(itemRow).append($(Td).html("<p>"+x.guest+"</p>"+x.dates))

                $(itemTb).append(itemRow);

                //SECOND ROW
                var itemRow2 = $("<tr>");
                $(itemRow2).append($('<td class="col1">').html("Rate : "+x.night_rate));
                $(itemRow2).append($('<td>').html("Current charges : "+x.due_amount));

                $(itemTb).append($(itemRow2));
                $(itemDiv).append(itemTb);

                $(".rooms-overview").append(itemDiv);
            });
        }
    })


    //room status chart


    // Radialize the colors
    Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function (color) {
        return {
            radialGradient: {
                cx: 0.5,
                cy: 0.3,
                r: 0.7
            },
            stops: [
                [0, color],
                [1, Highcharts.Color(color).brighten(-0.3).get('rgb')] // darken
            ]
        };
    });

    // Build the chart
    $('#room-chart').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Room Status '
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
         credits: {
            enabled: false
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color:'black'
                    },
                    connectorColor: 'silver'
                }
            }
        },
        series: [{
            name: 'Percentage',
            data: JSON.parse('{!!$room_chart!!}')
        }]
    });
});
</script>


<div class="col-md-9" style="padding-left:0">

    <div class="color-box pink">

        <span class="icon"><i class="fa fa-cart-arrow-down"></i></span>
        <div class="text">
            <h6>TOTAL SALES</h6>
            <p>{{number_format($sales["fo_sales"]+$sales["pos_sales"]) }} Rwf</p>
            <p>F.O & POS Sales</p>
        </div>

    </div>

    <div class="color-box light-green">

        <span class="icon"><i class="fa fa-cart-arrow-down"></i></span>
        <div class="text">
            <h6>TOTAL CREDIT</h6>

            <p>Sales {{ number_format($sales["fo_credit"]+$sales["pos_credit"]) }} </p>
            <p>F.O & POS Credit</p>

        </div>

    </div>

    <div class="color-box blue">

        <span class="icon"><i class="fa fa-cart-arrow-down"></i></span>
        <div class="text">
            <h6>TOTAL PAID</h6>
            <p>{{number_format($sales["total_paid"]) }}</p>
            <p>Room posts ,Credits</p>
        </div>

    </div>



    <div class="clearfix"></div>
    <br />
    <div class="row sales-summary">
        <div class="col-md-3">
            <i class="fa fa-shopping-cart"></i>
            <div class="item-info">
                <h6>F.O SALES</h6>
                <p>{{number_format($sales["fo_sales"])}} RWF</p>
            </div>
        </div>

        <div class="col-md-3">
            <i class="fa fa-user"></i>
            <div class="item-info">
                <h6>F.O CREDIT</h6>
                <p>{{number_format($sales["fo_credit"])}} RWF</p>
            </div>
        </div>

        <div class="col-md-3">
            <i class="fa fa-usd"></i>
            <div class="item-info">
                <h6>POS SALES</h6>
                <p>{{number_format($sales["pos_sales"])}} RWF</p>
            </div>
        </div>

        <div class="col-md-3 last">
            <i class="fa fa-clock-o"></i>
            <div class="item-info">
                <h6>POS CREDIT</h6>
                <p>{{number_format($sales["pos_credit"])}} RWF</p>
            </div>
        </div>
    </div>
    <div class="home-chart" style="min-width: 310px; height: 350px; margin: 0 auto" id="container"></div>

    <br />
    <h4 class="text-center text-danger">Room Status</h4>
    <p class="text-center" style="margin-top:-3px;color:rgb(167, 167, 167)">Chart</p>

    <br />
    <div class="home-chart" style="min-width: 310px; height: 350px; margin: 0 auto" id="room-chart"></div>
    <h4 class="text-center text-danger">Occupied Rooms</h4>
    <p class="text-center" style="margin-top:-3px;color:rgb(167, 167, 167)">(Limited to 14 rooms)</p>
    <div class="rooms-overview row">

    </div>

    <br />
    <h4 class="text-center">Stock Summary</h4>
    <br />
    <?php $purchase_total = 0; $req_total = 0; ?>
    <div class="row">
        <div class="col-md-6">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th style="background:#fff" class="text-center" colspan="2">PURCHASES</th>
                    </tr>
                    <tr>
                        <th class="text-center" style="padding:2px">
                            Stock
                        </th>

                        <th class="text-center" style="padding:2px">Amount</th>
                    </tr>
                </thead>


                @if(isset($purchases) && count($purchases) > 0)
                @foreach($purchases as $purchase)
                <tr>
                    <td>{{$purchase->name}}</td>
                    <td>{{number_format($purchase->amount) }}</td>
                </tr>
                <?php $purchase_total += $purchase->amount; ?>
                @endforeach
                @else
                <tr><td colspan="2">No Data</td></tr>

                @endif

                <tfoot>
                    <tr>
                        <th>TOTAL</th>
                        <th>{{number_format($purchase_total)}}</th>
                    </tr>
                </tfoot>
            </table>


        </div>

        <div class="col-md-6">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th style="background:#fff" class="text-center" colspan="2">REQUISITIONS</th>
                    </tr>
                    <tr>
                        <th class="text-center" style="padding:2px">
                            Department
                        </th>

                        <th class="text-center" style="padding:2px">Amount</th>
                    </tr>
                </thead>


                @if(isset($requisitions) && count($requisitions) > 0)
                @foreach($requisitions as $requisition)
                <tr>
                    <td>{{$requisition->department_name}}</td>
                    <td>{{    number_format($requisition->amount) }}</td>
                </tr>
                <?php $req_total += $requisition->amount; ?>
                @endforeach
                @else
                <tr>
                    <td colspan="2">No Data</td>
                </tr>

                @endif

                <tfoot>
                    <tr>
                        <th>TOTAL</th>
                        <th>{{number_format($req_total)}}</th>
                    </tr>
                </tfoot>
            </table>


        </div>
    </div>
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
            <p style="font-size:11px;color:#ccc;margin-top:5px;margin-bottom:-5px" class="text-center">{{isset($exchangerates[0]) ? $exchangerates[0]->date :"" }}</p>

        </div>
    </div>



    <div class="widget" style="border: 1px solid rgb(163, 149, 173)">
        <p class="widget-title" style="background:#7C5590">Cash Balance</p>
        <table style="margin-bottom:0" class="table-responsive  table-striped table">
            @foreach($cashbooks as $book)
            <tr>
                <td>{{ $book->cashbook_name }}</td>
                <td> {{ number_format($book->balance) }}</td>
            </tr>

            @endforeach

        </table>
    </div>
    @if(\Auth::user()->level >= 9)

    <div class="widget" style="border: 1px solid rgb(61, 122, 92)">
        <p class="widget-title" style="background:rgb(85, 144, 115)">Security Camera</p>
        <div class="text-center">

            <img class="text-center" style="padding:10px" width="72" src="/assets/images/lens.png" />
            <p style="font-size:11px;margin-bottom:5px;color:#b0b0b0">Access to security Cameras</p>
        </div>

    </div>
    <div class="widget green">
        <p class="widget-title">User Activities</p>
        <div class="widget-text">
            <ul class="activities-list">
                @foreach($logs as $log)
                <?php  $tsp = strtotime($log->date); ?>

                <li>
                    <p>{{$log->username}} : {{ $log->action }}</p><i class="fa fa-clock-o"></i> {{ date("d/m/Y H:i",$tsp) }}
                    <span>{{ date("H",$tsp) }}</span>
                </li>
                @endforeach

            </ul>
        </div>
    </div>
    @endif

</div>


<div class="clearfix"></div>
@stop