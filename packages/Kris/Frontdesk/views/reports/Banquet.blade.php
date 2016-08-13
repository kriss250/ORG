@extends("Frontdesk::MasterIframe")
@section("contents")

@include("Frontdesk::reports.report-filter")

<div class="print-document">
    @include("Frontdesk::reports.report-print-header")
    <p class="report-title">Banquet Booking Report</p>

    <?php $bans = \Kris\Frontdesk\Banquet::all();

          $date1 = isset($_GET['startdate']) ? $_GET['startdate'] : \Kris\Frontdesk\Env::WD()->format("Y-m-d") ;
          $date2 = isset($_GET['enddate']) ? $_GET['enddate'] : \Kris\Frontdesk\Env::WD()->addDays(14)->format("Y-m-d") ;

          $startdate = new \Carbon\Carbon($date1);
          $enddate = new \Carbon\Carbon($date2);

          $count = $enddate->diff($startdate)->days+1;
          $booking = (new \Kris\Frontdesk\Banquet)->getBooking($startdate->format("Y-m-d"),$enddate->format("Y-m-d"));
          $hallsCounter = 0;
    ?>

    <table class="table table-striped table-bordered table-condensed">
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
            {{preg_replace("~[0-9](.*?)(\^)~","",$booking[$ban->banquet_name][$date->format("Y-m-d")])}}
            @endif
            </td>

            @endforeach
            <?php $date = $startdate->addDays(1); ?>
        </tr>
        @endfor
    </table>

    @include("Frontdesk::reports.report-footer")

</div>

@stop

