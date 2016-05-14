@if(count($bill)>0)
  
    <?php print '<?xml version="1.0" encoding="UTF-8"?>';?>
<Bill offtariff="{{ $bill[0]->status == \ORG\Bill::OFFTARIFF ? 1 : 0 }}" id="{{ $bill[0]->idbills }}">
    <header>
        <logo>{{ \ORG\Settings::$LOGO }}</logo>
        <email>{{ \ORG\Settings::$EMAIL }}</email>
        <phone>{{ \ORG\Settings::$PHONES }}</phone>
    </header>

    <customer>{{ $bill[0]->customer }}</customer>

    <printdate>{{ \ORG\Dates::ToDSPFormat(\ORG\Dates::$RESTODATE)." ".date("H:i:s") }}</printdate>

    <orderdate>{{ $bill[0]->date }}</orderdate>

    <biller>{{ $bill[0]->username }}</biller>

    <waiter>{{ $bill[0]->waiter_name }}</waiter>
    <items>
        @foreach($bill as $b)
            <Item code="{{ $b->EBM }}" name="{{ $b->product_name }}" qty="{{ $b->qty }}" uprice="{{ $b->unit_price }}" subtotal="{{    $b->product_total }}"></Item>
        @endforeach
    </items>
        <total>
        {{ $bill[0]->bill_total }}
        </total>
    </Bill>
    @endif
