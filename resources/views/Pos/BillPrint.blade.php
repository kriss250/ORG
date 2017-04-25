

@if(count($bill)>0)
  <div class="bill_print">
{!! ($bill[0]->status == 2) ? ' <img class="paid_bg" src="/assets/images/paid_bg.png"> ' : '' !!}
  <div class="the_content">
      <table class="bill-header-table">

     <tr>
         <td>
            <div class="logo"><img width="60" src="{{\App\POSSettings::get("logo")[0] }}"> </div>
        </td>
         <td>
            <div style="font-weight:bold" class="bill_contacts">
                <p style="font-size:10px;" class="text-center"><i class="fa fa-envelope-o"></i> {{ \App\POSSettings::get("email") }}</p>
                <p class="text-center" style="font-size:10px !important"><i class="fa fa-phone"></i> {{\App\POSSettings::get("phone1")[0] }}</p>
              <p style="font-size:10px" class="text-center">TIN/TVA : {{\App\POSSettings::get("tin") }}</p>  

            </div>
         </td>
         
         </tr>
           </table>
            <div class="clearfix"></div>
             <p class="text-left" style="margin-top:3px;font-size:10px;margin-bottom:0"><b>Order No.: {{ $bill[0]->idbills }} - Customer : {{ $bill[0]->customer }}</b></p>
            <p style="font-size:10px;" class="text-left">Print Date: {{ \ORG\Dates::ToDSPFormat(\ORG\Dates::$RESTODATE)." ".date("H:i:s") }}</p>

      <table>
          <thead>
              <tr>
                  <th>Code</th>
                  <th>Qty</th>
                  <th>Order</th>
                  <th>U.Price</th>
                  <th>Total</th>
              </tr>
          </thead>

          @foreach($bill as $b)
                <tr>
                    <td>{{ $b->EBM }}</td>
                    <td>{{ $b->qty }}</td>
                    <td>{{ $b->product_name }}</td>
                    <td>{{ $b->unit_price }}</td>
                    <td>{{ $b->product_total }}</td>
                </tr>

          @endforeach
      </table>

            <div class="bill_summary">
               
                @if($b->status == \ORG\Bill::OFFTARIFF)
                    <p style="display: table; float: right;" class="text-strike" style="text-decoration:line-through">Total : 0</p>
                    <p style="font-size:11px;margin:0">Free Consumption</p>
                @else 
                    <p style="display: table; float: right;">Total : <span style="padding: 3px;border:1px solid #000;display: inline-block;">{{ $bill[0]->bill_total }}</span></p>
                @endif

                @if($bill[0]->status == 2)
                <div class="clearfix"></div>
                <span class="bill_pay_info">
                    <p>Amount paid : {{ $bill[0]->amount_paid}}</p>
                    <p>Change : {{ $bill[0]->change_returned}}</p>
                </span>
                <div class="clearfix"></div>
                @endif

                <p style="text-transform:capitalize;font-size:10px;" class="text-left">Biller : {{ $bill[0]->username }}</p>
                <p style="font-size:10px" class="text-left">Waiter : {{ $bill[0]->waiter_name }}</p>
                <p style="font-size:10px" class="text-left">Date : {{ $bill[0]->date }} </p>
                <p class="text-center" style="margin-top:10px;">.....................................</p>
            </div>

            <p style="font-size:10px;padding:10px 0;border-top:1px dashed" class="text-center">{{ \App\POSSettings::get("footer") ." ".\App\POSSettings::get("name") }}</p>
</div>
</div>

@endif