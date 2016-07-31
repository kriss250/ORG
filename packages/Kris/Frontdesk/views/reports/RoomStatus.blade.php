@extends("Frontdesk::MasterIframe")

@section("contents")

@include("Frontdesk::reports.report-filter")

<div class="print-document">

    @include("Frontdesk::reports.report-print-header")

    <p class="report-title">Room Status Report</p>
    <table class="table table-bordered table-striped table-condensed">
        <thead>
            <tr>
                <td style="max-width:20px">#</td>
                <td>Room</td>
                <td>
                    Room Type
                </td>

                <td>
                     Status
                </td>
            </tr>
        </thead>
        <?php $i=1; ?>
        @foreach($rooms as $room)
        <tr>
            <td style="max-width:20px">{{$i}}</td>
            <td>{{ $room->room_number }}</td>
            <td>{{ $room->type->type_name }}</td>
            <td>{{ $room->rstatus->status_name}}</td>
        </tr>
        <?php $i++; ?>
        @endforeach
    </table>

    @include("Frontdesk::reports.report-footer")

</div>

@stop

