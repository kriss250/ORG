@extends('Backoffice.Master')

@section("contents")

<div class="page-contents">
<div class="report-filter">
  <h3>Creditor Payment</h3>
  <p>Order Payment</p>
</div>
<Br>
<form class="form-inline" action="{{action("CreditsController@addPayment")}}" method="post">
  <input type="hidden" value="{{csrf_token()}}" name="_token" />
  <label for="">Voucher</label>
    <input autocomplete="off" type="text" class="form-control suggest-input" data-table="org_backoffice.credit" data-field="voucher" data-display-field="voucher" data-value-field="id" data-value-holder="#creditor_id" name="voucher" value="" />
<br>
<br>
    <input type="hidden" value="0" name="creditor_id" id="creditor_id" />
  <label for="">Amount</label>
    <input class="form-control" type="text" class="form-control" name="amount" />

    <label for="">Date</label>
      <input type="text" class="form-control date-picker" name="date" value="{{date("Y-m-d")}}">

      <label for="">Description</label>
        <input type="text" class="form-control" name="description" placeholder="Description">

      <hr>
      <input type="submit" name="name" class="btn btn-primary" value="Save" />
</form>

</div>

@stop
