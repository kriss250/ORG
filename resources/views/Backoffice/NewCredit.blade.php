@extends('Backoffice.Master')

@section("contents")

<div class="page-contents">
    <h2>New order on Credit</h2>
<p> Order Registration</p>

<hr />


    <form class="form-inline" action="{{action("CreditsController@store")}}" method="post">
      <label>Supplier / Creditor</label>
        <input autocomplete="off" name="creditor" data-table="org_backoffice.creditors" data-display-field="name" data-value-field="idcreditors" data-value-holder="#creditor_id" data-field="name" class="form-control suggest-input" placeholder="Supplier Name" />
        <input type="hidden" value="0" name="creditor_id" id="creditor_id" />
      <label>Voucher</label><br>
      <input name="voucher" placeholder="Voucher#" type="text" class="form-control" />
      <br>
      <label>Amount</label><br>
      <input name="amount" placeholder="Order Amount" type="text" class="form-control" />

      <label>Date</label>
      <input name="date" type="text" style="width:120px !important;max-width:300px !important;" class="form-control date-picker" value="{{date("Y-m-d")}}" />


      <br>
      <label>Description</label>
      <br>
      <textarea name="description" rows="3" class="form-control" cols="40"></textarea>
<p></p>

<input type="hidden" name="_token" value="{{csrf_token()}}" />

      <input type="submit" class="btn btn-primary" value="Save Order" />
    </form>

</div>

@stop
