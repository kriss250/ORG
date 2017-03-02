@if(count($order)>0)
<?php $resto = \App\Resto::get()->first(); print '<?xml version="1.0" encoding="UTF-8"?>';?>
<Order id="{{$order[0]->idorders }}">
    <header>
        <logo>{{\ORG\Settings::$LOGO }}</logo>
        <email>{{$resto->resto_email }}</email>
        <phone>{{$resto->resto_phone }}</phone>
        <tin>{{$resto->tin}}</tin>
        <website>{{$resto->website}}</website>
    </header>

    <printdate>{{\ORG\Dates::ToDSPFormat(\ORG\Dates::$RESTODATE)." ".date("H:i:s") }}</printdate>
    <orderdate>{{$order[0]->date }}</orderdate>
    <waiter>{{$order[0]->waiter_name }}</waiter>
    <table>{{$order[0]->table_name}}</table>
    <store>{{$order[0]->store_name}}</store>
    <items>
        @foreach($order as $b)
        <item name="{{$b->product_name }}" qty="{{$b->qty }}" uprice="{{$b->unit_price }}" subtotal="{{$b->product_total }}"></item>
        @endforeach
    </items>

</Order>
@endif
