@extends("Frontdesk::MasterIframe")
@section("contents")

@include("Frontdesk::reports.report-filter")
<div class="print-document">

    @include("Frontdesk::reports.report-print-header")
    <p class="report-title">Room Shift Report</p>
    <table class="table table-bordered table-striped table-condensed">
        <thead>
            <tr>
                <th>#</th>
                <th>Guest</th>
                <th>From Room</th>
                <th>To Room</th>
                <th>From Room Type</th>
                <th>
                    To Room Type
                </th>
                <th>From Rate</th>
                <th>
                    To Rate
                </th>

                <th>User</th>

                <th>Date</th>
            </tr>
        </thead>
        <?php $i=1; $total=0; ?>
        @foreach($data as $item)

        <tr title="Reservation : {{$item->reservation_id}}">
            <td>{{$i}}</td>
            <td>{{ $item->guest }}</td>
            <td>{{ $item->from_roomnumber }}</td>
            <td>{{ $item->to_roomnumber }}</td>
            <td>{{ $item->from_roomtype }}</td>
            <td>{{ $item->to_roomtype }}</td>
            <td>{{ $item->from_rate}}</td>
            <td>{{ $item->new_rate}}</td>
            <td>{{ $item->username}}</td>
            <td>{{ \App\FX::DT($item->date) }}</td>
        </tr>
        <?php $i++; ?>
        @endforeach

    </table>

    <div class="text-center print-footer">
        <table style="margin-bottom:85px;width:100%;" class="table">
            <tr>
                <td>
                    CASHIER
                </td>

                <td>
                    CONTROLLER
                </td>

                <td>
                    ACCOUNTANT
                </td>

                <td>
                    DAF
                </td>

                <td>
                    G. MANAGER
                </td>
            </tr>
        </table>
        <div class="clearfix"></div>
    </div>

</div>

@stop

