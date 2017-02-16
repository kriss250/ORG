@extends("Backoffice.Master")

@section("contents")
<div class="page-contents">

    <div class="report-filter">
        <table style="width:100%">
            <tr>
                <td>
                    <h3>
                        Transfers Overview {{ isset($selectedWarehouse) ? "(".$selectedWarehouse->name.")" : "" }}<br />
                       From To
                    </h3>
                </td>
                <td>
                    <form style="float:right" action="" class="form-inline" method="get">
                        <label>Date</label>
                        <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control" />
                        -
                        <input name="enddate" type="text" value="{{\ORG\Dates::$RESTODATE }}" class="date-picker form-control" />
                        <label>From Warehouse</label>
                        <select class="form-control" style="margin-right:15px" name="from_warehouse">
                            <option value="0">All</option>
                            @foreach($warehouses as $warehouse)
                            <option {{ isset($selectedWarehouse) && $selectedWarehouse->id== $warehouse->id ? " selected ": "" }} value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                            @endforeach
                        </select>

                        <label>To Warehouse</label>
                        <select class="form-control" style="margin-right:15px" name="to_warehouse">
                            <option value="0">All</option>
                            @foreach($warehouses as $warehouse)
                            <option {{ isset($selectedWarehouse) && $selectedWarehouse->id== $warehouse->id ? " selected ": "" }} value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                            @endforeach
                        </select>

                        <!--<label>Cat.</label>
                        <select class="form-control" style="max-width:100px; margin-right:15px" name="cat">
                            <option value="0">All</option>
                            @foreach(\App\StockReport::getCategories() as $cat)
                            <option {{ isset($selectedCategory) && $selectedCategory!=null && $selectedCategory->id== $cat->id ? " selected ": "" }} value="{{$cat->id}}">{{$cat->name}}</option>
                            @endforeach
                        </select>-->

                        
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
                <th>Code</th>
                <th>Name</th>
                <th>Quantity.</th>
                <th>U.Price</th>
                <th>Value</th>
            </tr>
        </thead>
        <?php $i =1;$purchases=0;$sales = 0; $total =0; ?>
        @foreach($data as $item)

        <tr>
            <td>{{$i}}</td>
            <td>{{$item->product_code}}</td>
            <td>{{$item->product_name}}</td>
            <td>{{$item->qty}}</td>
            <td>{{number_format($item->price)}}</td>
            <td>{{$item->amount}}</td>

        </tr>
        <?php $i++; $total += $item->amount; ?>
        @endforeach
        <tfoot>
            <tr>
                <th colspan="5">TOTAL</th>
                <th>{{number_format($total)}}</th>
            </tr>
        </tfoot>
    </table>
@endif


</div>

@stop