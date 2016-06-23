@extends('Backoffice.Master')

@section("contents")

<div class="page-contents">
    <div class="report-filter">
    <table style="width:100%">
        <tr>
            <td><h3>POS Customer Statement</h3> </td>
            <td>

               <form style="float:right" action="" class="form-inline" method="get">
                   <label>Date</label>
                   <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control" />-
                   <input name="enddate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control" />
                   <input type="submit" class="btn btn-success btn-sm" value="Submit" />
                   <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['enddate']) ? $_GET['enddate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="POS Customer Statement ({{$customer}})" class="btn btn-default report-print-btn">Print</button>

               </form> 
            </td>
        </tr>
            <tr>
          <td>
          <p class="text-danger"><b>Date : {{isset($_GET['startdate']) && isset($_GET['enddate'])  ?  \App\FX::Date($_GET['startdate'])." - ".\App\FX::Date($_GET['enddate']) : \App\FX::Date(\ORG\Dates::$RESTODATE) }}</b></p>
          </td>
        </tr>
    </table>
</div>
    <?php $paid=0;$due=0;?>
    <h4>Customer: {{$customer}}</h4>
  <table class="table table-condensed table-bordered">
      <thead>
          <tr>
              <th>#ID</th>
              <th>Amount</th>
              <th>Paid</th>
              <th>Balance</th>
              <th>Date</th>
          </tr>
      </thead>

      @foreach($data as $bill)
      <tr>
          <td>{{$bill->idbills}}</td>
          <td>{{number_format($bill->bill_total)}}</td>
          <td>{{$bill->amount_paid}}</td>
          <td>{{number_format($bill->bill_total-$bill->amount_paid)}}</td>
          <td>{{$bill->date}}</td>
      </tr>
      <?php $paid += $bill->amount_paid; $due +=$bill->bill_total; ?>
      @endforeach
      <tr>
          <th>TOTAL</th>
          <th>{{number_format($due)}}</th>
          <th>{{number_format($paid)}}</th>
          <th colspan="2">{{number_format($due-$paid)}}</th>
      </tr>
  </table>
</div>
      
@stop