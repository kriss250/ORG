@extends("Frontdesk::MasterIframe")

@section("contents")
@include("Frontdesk::reports.report-filter")

<div class="print-document">
    @include("Frontdesk::reports.report-print-header")

    <p class="report-title">Refund Report</p>
    <?php $total = 0; ?>
    <table class="table table-condensed table-striped table-bordered">
        <thead>
            <tr>
                <th>Guest</th>
                <th>Room</th>
                <th>Amount</th>
                <th>User</th>
                <th>Date</th>
            </tr>
        </thead>

        @if(isset($refunds))

            @foreach($refunds as $refund) 
                <tr>
                    <td>{{$refund->reservation->guest->firstname}} {{$refund->reservation->guest->lastname}}</td>
                    <td>{{$refund->reservation->room->room_number}}</td>
                    <td>{{number_format($refund->debit)}}</td>
                    <td>{{$refund->user->username}}</td>
                    <td>{{$refund->date}}</td>
                    <?php $total += $refund->debit;?>
                </tr>
            @endforeach

<tfoot>
    <tr>
        <td colspan="2">TOTAL</td>
        <td>{{number_format($total)}}</td>
        <td></td>
        <td></td>
        </tr>
    </tfoot>
        @endif
    </table>
   
</div>

@stop
