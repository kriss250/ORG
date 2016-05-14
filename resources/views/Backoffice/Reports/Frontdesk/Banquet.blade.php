@extends(isset($_GET['import']) ? "ORGFrontdesk.Reports.Master" : "Backoffice.Master")

@section("contents")


<div class="page-contents">

    <div class="report-filter">
        <table style="width:100%">
            <tr>
                <td>
                    <h3>Banquet Orders Report </h3>
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
                        <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Banquet Orders Report" class="btn btn-default report-print-btn">Print</button>
                    </form>
                </td>
            </tr>

            <tr>
                <td>
                    <p class="text-danger">
                        <b>Date : {{isset($_GET['startdate']) && isset($_GET['enddate'])  ?  \App\FX::Date($_GET['startdate'])." - ".\App\FX::Date($_GET['enddate']) : \App\FX::Date(\ORG\Dates::$RESTODATE) }}</b>
                    </p>
                </td>
            </tr>

        </table>
    </div>

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
                <th>{{ number_format($paid) }}</th>
                <th colspan="3"></th>
            </tr>
        </tfoot>
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

