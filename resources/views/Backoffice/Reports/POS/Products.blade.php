@extends("Backoffice.Master")

@section("contents")
<div class="page-contents">

<div class="report-filter">
<table style="width:100%">
    <tr>
        <td><h3>Product sales Report </h3> </td>
        <td>
          <form style="float:right" action="" class="form-inline" method="get">
                <label>Date</label> 
                <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control"> - 
                <input name="enddate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control">
                <label>Store</label> 
                <select name="store" class="form-control"><option value="0">All Stores</option>
                    <?php 
                    $stores = \DB::select("select * from store");
                    ?>
                    
                    @foreach($stores as $store)
                    <option value="{{ $store->idstore}}">{{ $store->store_name }}</option>
                    @endforeach
                </select>
                <input type="submit" class="btn btn-success btn-sm" value="Go">
                 <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Products Sales Report" class="btn btn-default report-print-btn">Print</button>
           </form> 
        </td>
    </tr>

     <tr>
      <td>
      <p class="text-danger"><b>Date : {{ isset($_GET['startdate']) && isset($_GET['enddate'])  ?  \App\FX::Date($_GET['startdate'])." - ".\App\FX::Date($_GET['enddate']) : \App\FX::Date(\ORG\Dates::$RESTODATE) }}</b></p>
      </td>
    </tr>
    
</table>


</div>

<table class="table-bordered table">
<thead>
    <tr>
        <th>Product</th><th>U.P </th><th>Qty</th><th>Total</th><th>Store</th>
    </tr>
</thead>
<?php $GT = 0; ?>

    @foreach($data as $row)
    <tr>
    <td> {{$row->product_name}} <span style="font-size:26px" class="text-danger">{{$row->user_created == 1 ? "*" : "" }}</span> </td>
    <td> {{$row->unit_price }} </td>
    <td> {{$row->qty }}</td>
    <td> {{ number_format($row->qty * $row->unit_price) }} </td>
    <?php $GT +=($row->qty * $row->unit_price); ?>
    <td> {{ $row->store_name }} </td>
    </tr>
    @endforeach

    @if(isset($free))
    <tr><td colspan="3"><b>TOTAL</b></td><td>{{ number_format($GT-$free) }}</td></tr>
    @endif
</table>

<p><dt>
    <i>(*) Custom products created by cashiers</i>
</dt></p>
</div>
@stop