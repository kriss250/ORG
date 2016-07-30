@extends("Frontdesk::MasterIframe")


@section("contents")
@include("Frontdesk::reports.report-filter")
<div class="page-contents">

    @include("Frontdesk::reports.report-print-header")
    <p class="report-title">Room shift Report</p>
    <table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th>Guest</th>
                <th>From Room</th>
                <th>To Room</th>
                <th>From Room Type</th>
                <th>To Room Type</th>
                <th>Checkin</th>
                <th>Checkout</th>
                <th>User</th>
            </tr>
        </thead>

        @foreach($data as $shift)
        <tr>
            <td>{{$shift->guest}}</td>
            <td>{{$shift->from_roomnumber}}</td>
            <td>{{$shift->to_roomnubmer}}</td>
            <td>{{$shift->from_roomtype}}</td>
            <td>{{$shit->to_roomtype}}</td>
            <td>{{$shift->checkin }}</td>
            <td>{{$shift->checkout}}</td>
            <td>>{{$shift->username}}</td>
        </tr>
        @endforeach
       
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

