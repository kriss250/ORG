@if(count($order)>0)
<?php $resto = \App\Resto::get()->first(); print '<?xml version="1.0" encoding="UTF-8"?>';?>
<Order id="{{$order[0]->idorders }}">
    <header>
        <logo>{{\App\Settings::get("logo")[0] }}</logo>
        <email>{{\App\Settings::get("email") }}</email>
        <phone>{{\App\Settings::get("phones")[0] }}</phone>
        <tin>{{\App\Settings::get("tin")}}</tin>
        <website>{{\App\Settings::get("website")}}</website>
    </header>
    <printdate>{{\ORG\Dates::ToDSPFormat(\ORG\Dates::$RESTODATE)." ".date("H:i:s") }}</printdate>
    <orderdate>{{$order[0]->date }}</orderdate>
    <waiter>{{$order[0]->waiter_name }}</waiter>
    <table>{{$order[0]->table_name}}</table>
    <store>{{$order[0]->store_name}}</store>
    <items>
        @foreach($order as $b)
        <item name="{{ utf8_encode($b->product_name) }} {{strlen($b->side_dishes) > 0 ? " (with : ".$b->side_dishes.")" : "" }}" qty="{{$b->qty }}" uprice="{{$b->unit_price }}" subtotal="{{$b->product_total }}"></item>
        @endforeach
    </items>

</Order>
@endif
