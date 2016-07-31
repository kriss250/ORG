@extends("Frontdesk::MasterIframe")
@section("contents")

@include("Frontdesk::reports.report-filter")
<div class="print-document">
    @include("Frontdesk::reports.report-print-header")
    <p class="report-title">Room Service Report</p>
    <table class="table table-bordered table-striped table-condensed">
        <thead>
            <tr>
                <th>#</th>
                <th>Room Type</th>
                <th>Room</th>
                <th>Guest</th>
                <th>
                    Description
                </th>
                <th>Amount</th>
                <th>
                    User
                </th>

                <th>Date</th>
            </tr>
        </thead>
        <?php $i=1; $total=0; ?>
    @foreach($data as $item)
        <tr>
            <td>{{$i}}</td>
            <td>{{ $item->type_name }}</td>
            <td>{{ $item->room_number }}</td>
            <td>{{ $item->guest }}</td>
            <td>{{ $item->motif }}</td>
            <td>{{ $item->amount }}</td>
            <td>{{ $item->user}}</td>
            <td>{{ \App\FX::DT($item->date) }}</td>
        </tr>
        <?php $i++; $total +=$room->amount; ?>
    @endforeach

        <tfoot>
            <tr>
                <td colspan="5">TOTAL</td>
                <td>{{number_format($total)}}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <div class="text-center print-footer">
        <table style="margin-bottom:85px;width:100%;" class="table">
            <tr>
                <td>
                    Cashier
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

