@extends(isset($_GET['import']) ? "ORGFrontdesk.Reports.Master" : "Backoffice.Master")


@section("contents")

<div class="page-contents">

    <div class="report-filter">
        <table style="width:100%">
            <tr>
                <td>
                    <h3>Payment Control </h3>
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
                        <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Frontoffice Control" class="btn btn-default report-print-btn">Print</button>
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

