@extends("Backoffice.Master")

@section("contents")
<div class="page-contents">

<div class="report-filter">
<table style="width:100%">
    <tr>
        <td><h3>Stock Product Quantity </h3> </td>
        <td>
         <form style="float:right" action="" class="form-inline" method="get">
                
                <label>Stock</label> 
                <select name="warehouse" class="form-control">
                    <option value="0">All</option>
                    <?php
                        $warehouses = \DB::connection("mysql_stock")->select("select id,name from warehouses");
                    ?>

                    @if(isset($warehouses))
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{$warehouse->name}}</option>
                        @endforeach

                    @endif
                </select>
                <input type="submit" class="btn btn-success btn-sm" value="Go">
                 <button type="button" data-dates="{{ isset($_GET['date']) ? $_GET['date'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Stock Quantity" class="btn btn-default report-print-btn">Print</button>
           </form>  
        </td>
    </tr>
</table>

</div>

@if(isset($data) && count($data) > 0)
    <table class="table table-bordered table-stripped">
        <thead>
            <tr>
                <th>Stock</th>
                <th>Product Code</th>
                <th>Product Name</th>
                <th>Quantity</th>
            </tr>
        </thead>
        @foreach($data as $item)
            <tr>
                <td>{{$item->stock }}</td>
                <td>{{$item->code }}</td>
                <td>{{$item->name }}</td>
                <td>{{$item->quantity }}</td>
            </tr>
        @endforeach
    </table>
@endif


</div>

@stop