@extends('Backoffice.Master')

@section("contents")

<div class="page-contents">
    <h2>Invoice Payment</h2>
    @if(\Session::has("msg"))
    <div class="alert alert-success">
        <button data-toggle="dismiss" class="btn alert-dismiss">
            <i class="fa fa-times"></i>
        </button>
        {{\Session::get("msg")}}
    </div>
    @endif


<form class="form orm-inline" action="{{action("InvoicePaymentController@store")}}" method="post">

  <div class="row">
    <div class="col-xs-6">
        <label>Invoice N<sup>0</sup></label>
      <div style="max-width:200px;" class="input-group">

      <input data-table="org_backoffice.invoices" data-field="idinvoices" type="text" class="form-control suggest-input" />
      <span class="input-group-addon"><i class="fa fa-check"></i></span>
    </div>
    </div>

    <div class="col-xs-6">
    <p>Invoice of</p>

      <label>Date of Payment</label>
      <input name="date" style="width:160px !important;max-width:200px !important;" value="{{(new \Carbon\Carbon())->format("Y-m-d")}}" type="text" class="date-picker form-control" placeholder="Date"/>
    </div>
  </div>

<hr>

  <div class="row">
    <div class="col-xs-3">
      <label>Amount Paid</label>
      <input style="max-width:180px" placeholder="Enter amount" type="text" name="amount" class='form-control' value="">
    </div>

    <div class="col-xs-3">
      <label>Mode of Payment</label>
      <select name="mode" required class="form-control">
        <option value="">Choose</option>
        @foreach(\App\PayMode::all() as $mode)
          <option value="{{$mode->idpay_method}}">{{$mode->method_name}}</option>
        @endforeach
      </select>
    </div>

    <div class="col-xs-3">
      <label>VAT Retained</label>
      <div class="input-group">
          <span class="input-group-addon"><input type="checkbox"></span>
          <input class="form-control" readonly disabled type="text" placeholder="VAT" />

      </div>
    </div>


      <div class="col-xs-3">
        <label>WHT(With Hold Tax)</label>
        <div class="input-group">
            <span class="input-group-addon"><input type="checkbox"></span>
            <input class="form-control" readonly disabled type="text" placeholder="WHT" />

        </div>

      </div>
</div>

<div class="clearfix"></div>


<label>
  Description
</label>

  <textarea name="name" class="form-control" rows="3" cols="40"></textarea>


<p>&nbsp;</p>
<input type="submit" class="btn btn-primary" value="Save Payment" />
</form>
</div>

@stop
