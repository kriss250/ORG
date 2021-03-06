@extends("Backoffice.Master")

@section("contents")
<div class="page-contents">

    <div class="report-filter">
        <table style="width:100%">
            <tr>
                <td>
                    <h3>
                        Stock Overview {{ isset($selectedWarehouse) ? "(".$selectedWarehouse->name.")" : "" }}<br />
                        Category : {{ isset($selectedCategory) && $selectedCategory!=null ? "(".$selectedCategory->name.")" : "All" }}
                    </h3>
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

                        <label>Cat.</label>
                        <select class="form-control" style="max-width:100px; margin-right:15px" name="cat">
                            <option value="0">All</option>
                            @foreach(\App\StockReport::getCategories() as $cat)
                            <option {{ isset($selectedCategory) && $selectedCategory!=null && $selectedCategory->id== $cat->id ? " selected ": "" }} value="{{$cat->id}}">{{$cat->name}}</option>
                            @endforeach
                        </select>

                        
                        <br />
                        <div style="margin-top:10px;" class="pull-right col-xs-3">
                            <input type="submit" class="btn btn-success btn-sm" value="Go" />
                            <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{isset($_GET['enddate']) ? $_GET['enddate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Stock Overview  {{ isset($selectedWarehouse) ? "(".$selectedWarehouse->name.")" : "" }}  / Category : {{ isset($selectedCategory) && $selectedCategory!=null ? "(".$selectedCategory->name.")" : "All" }}" class="btn btn-default report-print-btn">Print</button>
                        </div>
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
                <!--<th>Stock Quantity</th>-->
                <th>Opening St.</th>
                <th>Stock In</th>
                <th>Stock out</th>
                <th>Purch. Val.</th>
                <th>Sales. Val.</th>
                <th>Damaged</th>
                <th>Inventory</th>
                <th>Final Stock</th>
            </tr>
        </thead>
        <?php $i =1;$purchases=0;$sales = 0; ?>
        @foreach($data as $item)
<?php $p = explode("#",$item->stockin); $s = explode("#",$item->stockout);
      $trin = (isset($item->trin) ? $item->trin : 0);
      $trout = (isset($item->trout) ? $item->trout : 0);
?>
        <tr>
            <td>{{$i}}</td>
            <td>{{$item->name}}</td>
            <td>{{$item->unit}}</td>
            <!--<td>{{$item->quantity}}</td>-->
            <td>{{$item->opening-$item->inv-$item->invy}}</td>
            <td>{{$p[0]+$trin}}</td> <!--IN-->
            <td>{{$s[0]+$trout}}</td><!--OUT-->
            <td>{{ number_format($p[1]) }}</td>
            <td>{{ number_format($s[1]) }}</td>
            <td>{{$item->damaged}}</td>
            <td>{{$item->inv > 0 ? "+".$item->inv : $item->inv}}</td>
            <td>{{    ($item->opening-$item->invy)-$item->damaged-$item->stockout+$item->stockin-$trout+$trin}}</td>
        </tr>
        <?php $purchases += $p[1]; $sales +=$s[1]; $i++; ?>

        @endforeach
<tfoot>
    <tr>
        <th colspan="6">Total</th>
        <th>{{ number_format($purchases) }}</th>
        <th>{{ number_format($sales) }}</th>
        <th colspan="3"></th>
    </tr>
    </tfoot>
    </table>
@endif


</div>

@stop