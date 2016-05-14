@extends("Backoffice.Master")

@section("contents")
<div class="page-contents">

<div class="report-filter">
<table style="width:100%">
    <tr>
        <td><h3>Damaged Products </h3> </td>
        <td>
         <form style="float:right" action="" class="form-inline" method="get">
                <label>Date</label> 
                <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control"> - 
                <input name="enddate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control">
  
                <input type="submit" class="btn btn-success btn-sm" value="Go">
                 <button type="button" data-dates="{{ isset($_GET['date']) ? $_GET['date'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Damaged Products" class="btn btn-default report-print-btn">Print</button>
           </form>  
        </td>
    </tr>
</table>

</div>

@if(isset($data) && count($data) > 0)
    <table class="table table-bordered table-stripped">
        <thead>
            <tr>
                <th>Product</th>
                <th>Damaged Qty</th>
                <th>Stock</th>
                <th>Date</th>
            </tr>
        </thead>
        @foreach($data as $item)
            <tr>
                <td>{{$item->product_name}}</td>
                <td>{{$item->quantity }}</td>
                <td>{{$item->stock }}</td>
                <td>{{$item->date }}</td>
            </tr>
        @endforeach
    </table>
@endif


</div>

@stop