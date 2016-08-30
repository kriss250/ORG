@extends("Backoffice.Master")

@section("contents")

<div class="page-contents">
<style>
.suggestions-wrapper {
  display: inline;
}
</style>

<div class="report-filter">
<table style="width:100%">
    <tr>
        <td><h3>Debtors Report </h3> </td>
        <td>
          <form style="float:right" action="" class="form-inline" method="get">
            <label>Customer</label>

                <input autocomplete="off" name="customer" type="text" value="{{isset($_GET['customer']) ? $_GET['customer'] : ""}}" data-table="org_backoffice.invoices" data-field="institution" class="form-control suggest-input" placeholder="Creditor name"  />

                <label>Date</label>
                <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control"> -
                <input name="enddate" type="text" value="{{\ORG\Dates::$RESTODATE }}" class="date-picker form-control">

                        @if(isset($_GET['import']))
                        <input type="hidden" name="import" value="" />
                        @endif

                <input type="submit" class="btn btn-success btn-sm" value="Go">
                 <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Creditors Report {{isset($_GET['creditor']) ? "(".$_GET['creditor'].")" : ""}}" class="btn btn-default report-print-btn">Print</button>
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

<table class="table table-bordered table-striped table-condensed">
    <thead>
        <tr>

            <th rowspan="2">Customer</th>
            <th rowspan="2">Invoice #</th>
            <th rowspan="2">Due Date</th>
            <th rowspan="2">
              Description
            </th>
            <th rowspan="2">
              Delivery D.
            </th>
            <th class="text-center" colspan="4">
              Payments
            </th>

            <th rowspan="2">W. VAT</th>
            <th rowspan="2">WHT</th>
            <th rowspan="2">Amount</th>
            <th rowspan="2">Paid Amount</th>
            <th rowspan="2">Balance</th>
        </tr>

        <tr>
            <th>Check</th><th>Bank O.</th><th> Cash </th><th> CC</th>
        </tr>
        </thead>

        <?php
        $i=1; $dues= 0;$payments = 0; $pays = null;
        $totals =  ["cash"=>0,"bank"=>0,"check"=>0,"cc" => 0,"wht"=>0,"wh_vat"=>0,"paid"=>0,"dues"=>0];
         ?>
        @foreach($data as $item)
          <?php $pays = ["cash"=>0,"bank"=>0,"check"=>0,"cc" => 0,"wht"=>0,"wh_vat"=>0]; ?>
          <tr>

              <td>{{$item->institution}}</td>
              <td>{{$item->idinvoices}}/{{(new \Carbon\Carbon($item->created_at))->format("Y")}}</td>
              <td>
                {{$item->due_date}}
              </td>
              <td>
                {{$item->description}}
              </td>
              <td></td>
              <?php
              foreach($item->payment as $payment):
                $pays['wht'] += $payment->wht;
                $pays["wh_vat"] +=  $payment->wh_vat;
                $totals['wht'] +=$payment->wht;
                $totals['wh_vat'] +=$payment->wh_vat;

                switch ($payment->pay_mode) {
                  case '1':
                    $pays['cash'] += $payment->amount;
                      $totals['cash'] += $payment->amount;
                    break;
                  case '2':
                    $pays['cc'] += $payment->amount;
                      $totals['cc'] += $payment->amount;
                    break;
                  case '3':
                    $pays['check'] += $payment->amount;
                      $totals['check'] += $payment->amount;
                    break;
                  case '4':
                    $pays['bank'] += $payment->amount;
                      $totals['bank'] += $payment->amount;
                    break;
                }

              endforeach;
             ?>

             <td>{{number_format($pays['check'])}}</td>
             <td>{{number_format($pays['bank'])}}</td>
             <td>{{number_format($pays['cash'])}}</td>
             <td>{{number_format($pays['cc'])}}</td>
             <td>{{number_format($pays['wh_vat'])}}</td>
             <td>{{number_format($pays['wht'])}}</td>
             <td>
               {{number_format($item->amount)}}
             </td>
             <?php $TotalPays = array_sum($pays); $totals['paid'] += $TotalPays; $totals["dues"] += $item->amount; ?>
             <td>
               {{number_format($TotalPays)}}
             </td>
             <td>
               {{number_format($item->amount-$TotalPays)}}
             </td>
          </tr>

          <?php $i++; ?>
        @endforeach


<tfoot>
    <tr>
      <th  colspan="5">
        TOTAL
      </th>

      <th>
          {{number_format($totals['check'])}}
      </th>

      <th>
          {{number_format($totals['bank'])}}
      </th>
      <th>{{number_format($totals['cash'])}}</th>
        <th>{{number_format($totals['cc'])}}</th>
          <th>{{number_format($totals['wh_vat'])}}</th>
          <th>{{number_format($totals['wht'])}}</th>

          <th>{{number_format($totals['dues'])}}</th>
          <th>{{number_format($totals['paid'])}}</th>
          <th>{{number_format($totals['dues']-$totals['paid'])}}</th>

    </tr>
</tfoot>

</table>

<div class="text-center print-footer">
       <table style="margin-bottom:85px;width:100%;" class="table">
           <tr>
               <td>
                   RECEPTIONIST
               </td>

               <td>
                   CONTROLLER
               </td>

               <td>
                   C.S.M.M
               </td>

               <td>
                   ACCOUNTANT
               </td>

               <td>
                   DAF
               </td>

               <td>
                   G. MANAGER
               </td>
           </tr>
       </table>
        <div class="clearfix"></div>
    </div>

</div>

@stop
