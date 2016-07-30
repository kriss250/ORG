@extends("Frontdesk::MasterIframe")

@section("contents")
@include("Frontdesk::reports.report-filter")

<div class="print-document">
    @include("Frontdesk::reports.report-print-header")
   <p class="report-title">{{isset($expected) ? "Expected " : ""}}Arrival Report</p>
    <table class="table table-bordered table-striped table-condensed">
        <thead>
            <tr>
                <th>#</th>
                <th>Guest</th>
                <th>Country</th>
                <th>Room</th>
                <th>
                    R.Type
                </th>

                <th>Company</th>
                <th>Checkin</th>
                <th>Checkout</th>
                <th>
                    Rate
                </th>

                <th>
                    Payer
                </th>
            </tr>
        </thead>
        <?php $i=1; ?>
    @foreach($data as $item)

        <tr>
            <td>{{$i}}</td>
            <td>{{ $item->guest }}</td>
            <td>{{ $item->country }}</td>
            <td>{{ $item->room_number }}</td>
            <td>{{ $item->type_name }}</td>
            <td>{{ strlen($item->name) > 0 ? $item->name : "WALKIN" }}</td>
            <td>{{ $item->checkin}}</td>
            <td>{{ $item->checkout}}</td>
            <td>{{ $item->night_rate}}</td>
            <td>{{ $item->payer}}</td>
        </tr>
        <?php $i++; ?>
    @endforeach
    </table>
    
    @include("Frontdesk::reports.report-footer")
</div>

@stop

