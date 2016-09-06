@extends("Frontdesk::MasterIframe")

@section("contents")

@include("Frontdesk::reports.report-filter")

<div class="print-document">

    @include("Frontdesk::reports.report-print-header")

    <p class="report-title">Breakfast Report</p>
    <table class="table table-bordered table-striped table-condensed">
        <thead>
            <tr>
                <td>#</td>
                <td>Guest</td>
                <td>Country</td>
                <td>Room</td>
                <td>
                    Room Type
                </td>

                <td>
                    Pax(a/c)
                </td>
            </tr>
        </thead>
        <?php $i=1; $adults = 0;$children = 0; ?>
        @foreach($data as $item)

        <tr>
            <td>{{$i}}</td>
            <td>{{ $item->guest }}</td>
            <td>{{ $item->country }}</td>
            <td>{{ $item->room_number }}</td>
            <td>{{ $item->type_name }}</td>
            <td>{{ $item->pax}}</td>
        </tr>
        <?php $i++; list($a,$c) = explode("/",$item->pax); $adults += $a; $children +=$c;   ?>
        @endforeach

        <tfoot>
          <tr>
            <th colspan="5">
              TOTAL
            </th>
            <th>
              {{$adults}}/{{$children}}
            </th>
          </tr>
        </tfoot>
    </table>

    @include("Frontdesk::reports.report-footer")

</div>

@stop
