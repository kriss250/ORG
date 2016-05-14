@extends("Backoffice.Master")

@section("contents")
<div class="page-contents">

    <div class="report-filter">
        <table style="width:100%">
            <tr>
                <td>
                    <h3>Purchases {{ isset($selectedWarehouse) ? "(".$selectedWarehouse->name.")" : "" }} </h3>
                </td>
                <td>
                    <form style="float:right" action="" class="form-inline" method="get">
                        <label>Date</label>
                        <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control" />
                        -
                        <input name="enddate" type="text" value="{{\ORG\Dates::$RESTODATE }}" class="date-picker form-control" />

                        <label>Stock</label>
                        <select class="form-control" style="margin-right:15px" name="warehouse">
                            <option value="0">All</option>
                            @foreach($warehouses as $warehouse)
                            <option {{ isset($selectedWarehouse) && $selectedWarehouse->id== $warehouse->id ? " selected ": "" }} value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                            @endforeach
                        </select>
                        <input type="submit" class="btn btn-success btn-sm" value="Go" />
                        <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Purchases  {{ isset($selectedWarehouse) ? "(".$selectedWarehouse->name.")" : "" }}" class="btn btn-default report-print-btn">Print</button>
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
                <th>Reference No.</th>
                <th>Supplier</th>
                <th>Received By</th>
                <th>Amount</th>
                <th>Note</th>
                <th>Date</th>
                <th>Open</th>
            </tr>
        </thead>
<?php $purchases = 0; ?>
@if(isset($data) && count($data) > 0)
        @foreach($data as $item)
            <tr>
                <td>{{$item->reference_no}}</td>
                <td>{{$item->supplier_name }}</td>
                <td>{{$item->user }}</td>
                <td>{{number_format($item->total) }}</td>
                 <td>{!! html_entity_decode($item->note) !!}</td>
                <td>{{$item->date }}</td>
                <td><a class="modal-btn" data-width="620" href="{{ action("BackofficeReportController@index","purchaseItems")}}?id={{$item->id}}"><i class="fa fa-expand"></i></a></td>
            </tr>

        <?php $purchases +=$item->total; ?>
        @endforeach
@endif

        <tfoot>
            <tr>
                <th colspan="3">Total</th>
                <th>{{ number_format($purchases) }}<th>
                <th><th>
            </tr>
        </tfoot>
    </table>



</div>

@stop