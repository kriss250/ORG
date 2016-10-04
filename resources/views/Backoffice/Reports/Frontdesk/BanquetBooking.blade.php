@extends("Backoffice.Master")
@section("contents")

<div class="page-contents">
    
    <?php
 $bans = \Kris\Frontdesk\Banquet::all();
          $date1 = isset($_GET['startdate']) ? $_GET['startdate'] : \Kris\Frontdesk\Env::WD()->format("Y-m-d") ;
          $date2 = isset($_GET['enddate']) ? $_GET['enddate'] : \Kris\Frontdesk\Env::WD()->addDays(14)->format("Y-m-d") ;
          $startdate = new \Carbon\Carbon($date1);
          $enddate = new \Carbon\Carbon($date2);
          $count = $enddate->diff($startdate)->days+1;
          $booking = (new \Kris\Frontdesk\Banquet)->getBooking($startdate->format("Y-m-d"),$enddate->format("Y-m-d"));
          $hallsCounter = 0;
    ?>
    <div class="report-filter">
        <table style="width:100%">
            <tr>
                <td>
                    <h3>Banquet Report </h3>
                </td>
                <td>
                    <form style="float:right" action="" class="form-inline" method="get">
                        <label>Date</label>
                        <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control" />-
                        <input name="enddate" type="text" value="{{\ORG\Dates::$RESTODATE }}" class="date-picker form-control" />
                        @if(isset($_GET['import']))
                        <input type="hidden" name="import" value="" />
                        @endif

                        <input type="submit" class="btn btn-success btn-sm" value="Go" />
                        <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Banquet Report" class="btn btn-default report-print-btn">Print</button>
                    </form>
                </td>
            </tr>

            <tr>
                <td>
                    <p class="text-danger">
                        <b>Date : {{ isset($_GET['startdate']) && isset($_GET['enddate'])  ?  \App\FX::Date($_GET['startdate'])." - ".\App\FX::Date($_GET['enddate']) : \App\FX::Date(\ORG\Dates::$RESTODATE) }}</b>
                    </p>
                </td>
            </tr>

        </table>
    </div>
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
        @for($i=0;$i
        <$count;$i++)
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

