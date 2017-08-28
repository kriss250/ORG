@if(count($bill)>0)
  
    <?php $resto = \App\Resto::get()->first(); print '<?xml version="1.0" encoding="UTF-8"?>';?>
<?php
$discount = 0;
if($bill[0]->is_fixed_discount==1){
    $discount = $bill[0]->discount;
}else {
    $discount = ($bill[0]->discount * $bill[0]->bill_total) / 100;
}
?>
<Bill discount="{{$discount}}" offtariff="{{ $bill[0]->status == \ORG\Bill::OFFTARIFF ? 1 : 0 }}" id="{{ $bill[0]->idbills }}">
    <header>
        <logo>{{ \App\Settings::get("logo")[0] }}</logo>
        <email>{{\App\Settings::get("email") }}</email>
        <phone>{{\App\Settings::get("phones")[0] }}</phone>
        <tin>{{\App\Settings::get("tin")}}</tin>
        <website>{{\App\Settings::get("website")}}</website>
    </header>

    <customer>{{ $bill[0]->customer }}</customer>

    <printdate>{{ \ORG\Dates::ToDSPFormat(\ORG\Dates::$RESTODATE)." ".date("H:i:s") }}</printdate>

    <orderdate>{{ $bill[0]->date }}</orderdate>

    <biller>{{ $bill[0]->username }}</biller>

    <waiter>{{ $bill[0]->waiter_name }}</waiter>
    <items>
        @foreach($bill as $b)
            <Item code="{{ $b->EBM }}" name="{{utf8_encode($b->product_name) }}" qty="{{ $b->qty }}" uprice="{{ $b->unit_price }}" subtotal="{{    $b->product_total }}"></Item>
        @endforeach
    </items>
        <total>
        {{ $bill[0]->bill_total }}
        </total>
    </Bill>
    @endif
