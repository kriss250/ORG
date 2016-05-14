@extends("Backoffice.Master")

@section("contents")
<div class="page-contents">
    <div class="report-filter">
<table style="width:100%">
    <tr>
        <td><h3>Stock - POS Relationship</h3> </td>
        <td>
          <form style="float:right" action="" class="form-inline" method="get">
     
     
                <input type="submit" class="btn btn-success btn-sm" value="Go">
                 <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Stock - POS Relationship" class="btn btn-default report-print-btn">Print</button>
           </form> 
        </td>
    </tr>
</table>


</div>

    @if(isset($_GET['a']))
    <?php 
    
        $sql = "SELECT product_name,ebm,price FROM org_pos.products 
     join product_price on price_id = product_price.id
    where user_created = 0 and category_id=1 and stock_id=0";
        $data = \DB::select($sql);
        
    
    ?>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Ebm</th>
                <th>Price</th>
            </tr>
        </thead>
    
    @foreach($data as $d)
        <tr>
            <td>{{ $d->product_name }}</td>
            <td>{{ $d->ebm }}</td>
            <td>{{ number_format($d->price) }}</td>
        </tr>
    @endforeach
        </table>

    @endif

    @if(isset($data) && !isset($_GET['a']))
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>POS ID</th>
                    <th>POS NAME</th>
                    <th>POS PRICE</th>
                    <th>STOCK NAME</th>
                    <th>Stock ID</th>
                </tr>
            </thead>

            @foreach($data as $item)
                 <tr>
                    <td>{{ $item->posID }}</td>
                    <td>{{ $item->pos_name}}</td>
                     <td>{{ number_format($item->price) }}</td>
                    <td>{{ $item->stock_name }}</td>
                    <td>{{ $item->stockID }}</td>
                </tr>
            @endforeach
        </table>
    @endif
</div>

@stop
