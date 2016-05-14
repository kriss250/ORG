@extends(isset($_GET['import']) ? "ORGFrontdesk.Reports.Master" : "Backoffice.Master")

@section("contents")

<div class="page-contents">

    <div class="report-filter">
        <table style="width:100%">
            <tr>
                <td>
                    <h3>Room Transfers</h3>
                </td>
                <td>
                    <form style="float:right" action="" class="form-inline" method="get">
                        <label>Date</label>
                        <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control" />
                        -
                        <input name="enddate" type="text" value="{{\ORG\Dates::$RESTODATE }}" class="date-picker form-control" />
                        @if(isset($_GET['import']))
                        <input type="hidden" name="import" value="" />
                        @endif

                        <input type="submit" class="btn btn-success btn-sm" value="Go" />
                        <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Room Transfers" class="btn btn-default report-print-btn">Print</button>
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

        <tr>
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

