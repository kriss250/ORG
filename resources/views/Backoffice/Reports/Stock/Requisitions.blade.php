@extends("Backoffice.Master")

@section("contents")
<div class="page-contents">

    <div class="report-filter">
        <table style="width:100%">
            <tr>
                <td>
                    <h3>Requisitions</h3>
                </td>
                <td>
                    <form style="float:right" action="" class="form-inline" method="get">
                        <label>Date</label>
                        <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control" />
                        -
                        <input name="enddate" type="text" value="{{\ORG\Dates::$RESTODATE }}" class="date-picker form-control" />

                        <input type="submit" class="btn btn-success btn-sm" value="Go" />
                        <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Requisitions" class="btn btn-default report-print-btn">Print</button>
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


    <table class="table table-bordered table-stripped">
        <thead>
            <tr>
                <th>#</th>
                <th>ID</th>
                <th>Prepared By</th>
                <th>Amount</th>
                <th>Note</th>
                <th>Date</th>
                <th>Open</th>
            </tr>
        </thead>
<?php $req = 0; $i=1; ?>
@if(isset($data) && count($data) > 0)
        @foreach($data as $item)
            <tr>
                <td>{{$i}}</td>
                <td>{{$item->idrequisition }}</td>
                <td>{{$item->user }}</td>
                <td>{{number_format($item->total) }}</td>
                 <td>{!! html_entity_decode($item->note) !!}</td>
                <td>{{$item->date }}</td>
            <td>
                <a class="modal-btn" data-width="620" href="{{ action("BackofficeReportController@index","requisitionItems")}}?id={{$item->idrequisition }}">
                    <i class="fa fa-expand"></i></a></td>
            </tr>

        <?php $req +=$item->total; $i++; ?>
        @endforeach
@endif

        <tfoot>
            <tr>
                <th colspan="3">Total</th>
                <th>{{ number_format($req) }}<th>
                <th><th>
            </tr>
        </tfoot>
    </table>



</div>

@stop