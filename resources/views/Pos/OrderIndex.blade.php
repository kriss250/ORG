@extends('/Pos/OrderMaster')

@section("contents")
<h1 class="text-center">Captain Order</h1>
<p class="text-center">Restaurant Ordering System</p>
<br />
<br />
<ul class="order-menu">

    <li>
        <a href="{{route('newOrder')}}">
            <i class="fa fa-plus-circle"></i>
            New Order
        </a>
    </li>

    <li>
        <a href="{{route('newOrder')}}">
            <i class="fa fa-list-ol"></i>
            My Orders
        </a>
    </li>

    <li>
        <a href="{{route('newOrder')}}">
            <i class="fa fa-key"></i>
            Change PIN
        </a>
    </li>
</ul>

<br />
<br />
@stop