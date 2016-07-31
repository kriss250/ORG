@extends("Frontdesk::MasterIframe")

@section("contents")

@include("Frontdesk::reports.report-filter")

<div class="print-document">

    @include("Frontdesk::reports.report-print-header")

    <p class="report-title">Laundry Report</p>
    <table class="table table-bordered table-striped table-condensed">
        <thead>
            <tr>
                <td style="max-width:20px">#</td>
                <td>Room</td>
                <td>
                    Guest
                </td>
                <td>
                    Items
                </td>
                <td>
                     Amount
                </td>
                <td>
                    User
                </td>
            </tr>
        </thead>
        <?php $i=1; $total = 0; ?>
        @foreach($orders as $order)
        <tr>
            <td style="max-width:20px">{{$i}}</td>
            <?php
            $room = $order->reservation->room;
            $guest = $order->reservation->guest;
            $total += $order->amount;
            ?>
            <td>{{$room->room_number}}</td>
            <td>{{$guest->firstname}} {{$guest->lastname}}</td>
            <td>{{$order->items}}</td>
            <td>{{$order->amount}}</td>
            <td>{{$order->user->username}}</td>
        </tr>
        <?php $i++; ?>
        @endforeach

        <tfoot>
            <tr>
                <td colspan="4">
                    TOTAL
                </td>
                <td class="text-right text-bold" colspan="2">
                    {{number_format($total)}}
                </td>
            </tr>
        </tfoot>
    </table>

    @include("Frontdesk::reports.report-footer")

</div>

@stop

