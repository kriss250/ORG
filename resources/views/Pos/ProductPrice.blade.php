@extends("Pos.master")

@section("contents")

<br/>
<h2>Product Prices</h2>
<p class="page_info">
    <i class="fa fa-info-circle"></i>Update multipe products at the same time.
</p>

<table class="table table-condensed table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
        </tr>
    </thead>
    <form method="post" action="{{action("ProductsController@productPriceUpdate")}}">
        @foreach($products as $product)
        <tr>
            <td>{{$product->id}}</td>
            <td>{{$product->product_name}}</td>
            <td><input type="text" name="pc_{{$product->price->id}}" value="{{$product->price->price}}" /></td>
        </tr>
        @endforeach
        </table>
        <input class="btn btn-success" type="submit" value="Update" />
        <input type="hidden" name="_token" value="{{csrf_token()}}" />
    </form>

@stop