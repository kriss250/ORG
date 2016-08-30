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
        <td><h3>Creditors Report </h3> </td>
        <td>
          <form style="float:right" action="" class="form-inline" method="get">
            <label>Creditor</label>

                <input autocomplete="off" name="creditor" type="text" value="{{isset($_GET['creditor']) ? $_GET['creditor'] : ""}}" data-table="org_backoffice.creditors" data-field="name" class="form-control suggest-input" placeholder="Creditor name"  />

                <label>Date</label>
                <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control"> -
                <input name="enddate" type="text" value="{{\ORG\Dates::$RESTODATE }}" class="date-picker form-control">

                        @if(isset($_GET['import']))
                        <input type="hidden" name="import" value="" />
                        @endif

                <input type="submit" class="btn btn-success btn-sm" value="Go">
                 <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Debtors Report {{isset($_GET['creditor']) ? "(".$_GET['creditor'].")" : ""}}" class="btn btn-default report-print-btn">Print</button>
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
            <th>#</th>
            <th>Creditor</th>
            <th>Voucher</th>
            <th>
              Description
            </th>
            <th>Amount</th>
            <th>Paid Amount</th>
            <th>Balance</th>
        </tr>
        </thead>
        <?php $i=1; $dues= 0;$payments = 0; ?>
        @foreach($data as $item)
          <tr>
              <td>{{$i}}</td>
              <td>{{$item->name}}</td>
              <td>{{$item->voucher}}</td>
              <td>
                {{$item->description}}
              </td>
              <td>{{$item->amount}}</td>

              <?php $paid = $item->paid_amount; ?>
              <?php $dues += $item->amount; $payments += $paid; ?>

              <td>{{$paid}}</td>
              <td>{{$item->amount-$paid}}</td>
          </tr>

          <?php $i++; ?>
        @endforeach


<tfoot>
    <tr>
      <th  colspan="4">
        TOTAL
      </th>

      <th>
          {{number_format($dues)}}
      </th>

      <th>
        {{number_format($payments)}}
      </th>
      <th>
        {{number_format($dues-$payments)}}
      </th>
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
