@extends("Frontdesk::MasterIframe")
@section("contents")

@include("Frontdesk::reports.report-filter")

<div class="print-document">

    <p class="report-title">Arrival Report</p>

    <table class="table table-bordered table-condensed table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Hall</th>
                <th>Theme</th>
                <th>Guest</th>
                <th>Company</th>
                <th>Arrival</th>
                <th>Departure</th>
               
                <th>Rate</th>
                <th>Paid</th>
                <th>User</th>
                <th>Date</th>
                <th>Note</th>
            </tr>
        </thead>
        <?php $i =  1; $rates = 0; $paid= 0; ?>
        @foreach($data as $item)
        <tr>
            <td>{{ $i }}</td>
            <td>{{$item->banquet_name}}</td>
            <td>{{$item->theme_name}}</td>
            <td>{{$item->guest}}</td>
            <td>{{$item->company}}</td>
            <td>{{$item->arrival}}</td>
            <td>{{$item->departure }}</td>

            <td>{{number_format($item->total_rate)}}</td>
            <td>{{number_format($item->paid)}}</td>
            <td>{{$item->username}}</td>
            <td>{{$item->date}}</td>
            <td>{{$item->note}}</td>
        </tr>

        <?php $i++; $rates += $item->total_rate; $paid +=$item->paid; ?>
        @endforeach

        <tfoot>
            <tr>
                <th colspan="7">Total</th>
                <th>{{ number_format($rates) }}</th>
                <th>{{number_format($paid) }}</th>
                <th colspan="3"></th>
            </tr>
        </tfoot>
    </table>



    @include("Frontdesk::reports.report-footer")

</div>

@stop

