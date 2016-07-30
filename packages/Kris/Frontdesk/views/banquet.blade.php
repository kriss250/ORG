@extends("Frontdesk::MasterIframe")

@section("contents")

<div class="panel-desc">
    <p class="title">Banquet Booking</p>
    <p class="desc"></p>
</div>

<?php $bans = \Kris\Frontdesk\Banquet::all();

    $date1 = isset($_GET['startdate']) ? $_GET['startdate'] : \Kris\Frontdesk\Env::WD()->format("Y-m-d") ;
    $date2 = isset($_GET['enddate']) ? $_GET['enddate'] : \Kris\Frontdesk\Env::WD()->addDays(14)->format("Y-m-d") ;

      $startdate = new \Carbon\Carbon($date1);
      $enddate = new \Carbon\Carbon($date2);

      $count = $enddate->diff($startdate)->days+1;
      $booking = (new \Kris\Frontdesk\Banquet)->getBooking($startdate->format("Y-m-d"),$enddate->format("Y-m-d"));
      $hallsCounter = 0;
?>

<div class="list-filter">
    <?php $wd = \Kris\Frontdesk\Env::WD(); ?>
    <form action="" method="get" class="form-inline">
        

        <fieldset class="bordered">
            <label>From Date</label>
            <input type="text" name="startdate" value="{{$startdate->format("Y-m-d")}}" class="form-control datepicker" placeholder="From" />
        </fieldset>

        <fieldset class="bordered">
            <label>To Date</label>
            <input type="text" name="enddate" value="{{$enddate->format("Y-m-d")}}" class="form-control datepicker" placeholder="To" />
        </fieldset>

        <button title="Banquet Event" onclick="window.openDialog('{{action("\Kris\Frontdesk\Controllers\OperationsController@forms",'banquetEvent')}}','Banquet','width=410,height=350,resizable=no',this)" class="btn btn-primary btn-xs" type="button">Add Event</button>
        <input type="submit" value="Find" class="btn btn-success btn-xs" />
    </form>

    <div class="clearfix"></div>
</div>

<p>Booking</p>

<table class="table table-stripped table-bordered table-condensed">
    <thead>
        <tr>
            <td>Date</td>
            @foreach($bans as $ban)
            <td>{{$ban->banquet_name}}</td>
            <?php  $hallsCounter++; ?>
            @endforeach
        </tr>
    </thead>
    <?php $date = $startdate; ?>
    @for($i=0;$i<$count;$i++)
    <tr>

        <td>
            
            {{$date->format("d/m/Y")}}
        </td>
        @foreach($bans as $ban)
        

        <td>
            @if(isset($booking[$ban->banquet_name][$date->format("Y-m-d")]))
            {{$booking[$ban->banquet_name][$date->format("Y-m-d")]}}
            @endif
        </td>
       
        @endforeach
        <?php $date = $startdate->addDays(1); ?>
    </tr>
    @endfor
</table>

@stop