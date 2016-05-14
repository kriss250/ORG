@extends(isset($_GET['import']) ? "ORGFrontdesk.Reports.Master" : "Backoffice.Master")

@section("contents")

<div class="page-contents">

    <div class="report-filter">
        <table style="width:100%">
            <tr>
                <td>
                    <h3>Service Sales </h3>
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
                        <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Service Sales" class="btn btn-default report-print-btn">Print</button>
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
        <?php $i++; $total +=$item->amount; ?>
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

