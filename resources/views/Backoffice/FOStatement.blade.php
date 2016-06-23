@extends('Backoffice.Master')

@section("contents")

<div class="page-contents">
    <div class="report-filter">
        <table style="width:100%">
            <tr>
                <td><h3>F.O Customer Statement</h3> </td>
                <td>

                    <form style="float:right" action="" class="form-inline" method="get">
                        <label>Date</label>
                        <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control" />-
                        <input name="enddate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control" />
                        <input type="submit" class="btn btn-success btn-sm" value="Submit" />
                        <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['enddate']) ? $_GET['enddate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="F.O Customer Statement ({{$customer}})" class="btn btn-default report-print-btn">Print</button>

                    </form>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="text-danger"><b>Date : {{isset($_GET['startdate']) && isset($_GET['enddate'])  ?  \App\FX::Date($_GET['startdate'])." - ".\App\FX::Date($_GET['enddate']) : \App\FX::Date(\ORG\Dates::$RESTODATE) }}</b></p>
                </td>
            </tr>
        </table>
    </div>
    <?php $paid=0;$i=1;$acco=0; $services=0;$total = 0;$bal=0;?>
    <h4>Customer: {{$customer}}</h4>

    @if(isset($data) && $data != null)
    <table class="table table-striped table-condensed table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Res. ID</th>
                <th>Room</th>
                <th>Guest</th>
                <th>Rate</th>
                <th>Checkin</th>
                <th>Checkout</th>
                <th>Accomo.</th>
                <th>Services</th>
                <th>Total</th>
                <th>Paid</th>
                <th>Balance</th>
            </tr>
        </thead>

        @foreach($data as $item)
        <tr>
            <td>{{$i}}</td>
            <td>{{$item->idreservation}}</td>
            <td>{{$item->room_number}}</td>
            <td>{{$item->guest}}</td>
            <td>{{$item->night_rate}}</td>
            <td>{{\App\FX::Date($item->checkin)}}</td>
            <td>{{ \App\FX::Date($item->checkout)}}</td>
            <td>{{number_format($item->acco)}}</td>
            <td>{{number_format($item->services)}}</td>
            <td>{{number_format($item->acco+$item->services)}}</td>
            <td>{{number_format($item->paid)}}</td>
            <td>{{number_format($item->acco+$item->services-$item->paid)}}</td>
        </tr>

        <?php $i++;$acco += $item->acco; $services += $item->services; $paid += $item->paid;?>
        @endforeach

        <tr>
            <th colspan="7">TOTAL</th>
            <th>{{number_format($acco)}}</th>
            <th>{{number_format($services)}}</th>
            <th>{{number_format($acco+$services)}}</th>
            <th>{{number_format($paid)}}</th>
            <th>{{number_format($acco+$services-$paid)}}</th>
        </tr>
    </table>
    @else
    <h4>No Data available</h4>

    @endif
</div>

@stop