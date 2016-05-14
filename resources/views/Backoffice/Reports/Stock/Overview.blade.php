@extends("Backoffice.Master")

@section("contents")
<div class="page-contents">

    <div class="report-filter">
        <table style="width:100%">
            <tr>
                <td>
                    <h3>Stock Overview {{ isset($selectedWarehouse) ? "(".$selectedWarehouse->name.")" : "" }} </h3>
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
                        <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Stock Overview  {{ isset($selectedWarehouse) ? "(".$selectedWarehouse->name.")" : "" }}" class="btn btn-default report-print-btn">Print</button>
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

@if(isset($data) && count($data) > 0)
    <table class="table table-bordered table-stripped table-condensed">
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Unit</th>
                <th>Stock Quantity</th>
                <th>Opening St.</th>
                <th>Stock In</th>
                <th>Stock out</th>
                <th>Purch. Val.</th>
                <th>Sales. Val.</th>
                <th>Damaged</th>
                <th>Final Stock</th>
            </tr>
        </thead>
        <?php $i =1;$purchases=0;$sales = 0; ?>
        @foreach($data as $item)
<?php $p = explode("#",$item->stockin); $s = explode("#",$item->stockout); ?>
        <tr>
            <td>{{$i}}</td>
            <td>{{$item->name}}</td>
            <td>{{$item->unit}}</td>
            <td>{{$item->quantity}}</td>
            <td>{{$item->opening}}</td>
            <td>{{$p[0]}}</td>
            <td>{{  $s[0] }}</td>
            <td>{{ number_format($p[1]) }}</td>
            <td>{{ number_format($s[1]) }}</td>
            <td>{{$item->damaged}}</td>
            <td>{{$item->opening-$item->damaged-$item->stockout+$item->stockin}}</td>
        </tr>
        <?php $purchases += $p[1]; $sales +=$s[1]; $i++; ?>

        @endforeach
<tfoot>
    <tr>
        <th colspan="7">Total</th>
        <th>{{ number_format($purchases) }}</th>
        <th>{{ number_format($sales) }}</th>
        <th colspan="2"></th>
    </tr>
    </tfoot>
    </table>
@endif


</div>

@stop