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

        <h2>Cash Books</h2> 

        <?php
        $_cashbooks =  DB::connection("mysql_backoffice")->select("SELECT * FROM cash_book order by cashbookid desc");
        ?>

        <p>Cashbooks Balance</p>

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Cashbook</th>
                <th>Balance</th>
                <th>Action</th>
            </tr>
        </thead>

        @foreach($_cashbooks as $book)
            <tr>
                <td>
                    {{ $book->cashbook_name }}
                </td>
                <td>
                    {{ number_format($book->balance) }}
                </td>
                		 	<td><a class="btn btn-xs" href="{{ action("CashbookController@show",$book->cashbookid) }}" style="font-size:12px;">Open</a>

            </tr>
        @endforeach
    </table>
    <div class="clearfix"></div>
    <br />



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