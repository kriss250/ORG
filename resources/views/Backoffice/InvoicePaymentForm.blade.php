@extends('Backoffice.Master')

@section("contents")

<script>
  $(document).ready(function(){
    $(".vat_check").click(function(){
      var input = $(this).parent().parent().find("input[type='text']");
      if($(this).prop("checked"))
      {
        var amount = parseFloat($("[name='amount']").val());
        var vat =Math.ceil(calculateVAT(amount));
        $(input).val(vat);
        $(input).prop({disabled:false});
      }else {
        $(input).val("");
        $(input).prop({disabled:true})
      }
    });


    $(".wht_check").click(function(){
      var input = $(this).parent().parent().find("input[type='text']");
      if($(this).prop("checked"))
      {
        var amount = parseFloat($("[name='amount']").val());
        var vat =Math.ceil(calculateWHT(amount));
        $(input).val(vat);
        $(input).prop({disabled:false})
      }else {
        $(input).val("");
        $(input).prop({disabled:true});
      }
    })

  })

  function calculateVAT(noVATAmount)
  {
    var vatRate = 18/118;
    var amountVat = noVATAmount*vatRate;
    var vat = amountVat/(1-vatRate);
    return vat;
  }

  function calculateWHT(noVATAmount)
  {
    var vatRate = 3/100;
    var amountVat = noVATAmount*vatRate;
    var vat = amountVat/(1-vatRate);
    return vat;
  }
</script>
<div class="page-contents">
    <h2>Invoice Payment</h2>


<form class="form" action="{{action("InvoicePaymentController@store")}}" method="post">
<input type="hidden" name="_token" value="{{csrf_token()}}" />
  <div class="row">
    <div class="col-xs-6">
        <label>Invoice N<sup>0</sup></label>
      <div style="max-width:200px;" class="input-group">

      <input name="invoice" value="{{isset($_GET['id']) ? $_GET['id'] : old("invoice")}}" data-table="org_backoffice.invoices" data-field="idinvoices" type="text" class="form-control suggest-input" />
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
      <input value="{{old("amount")}}" style="max-width:180px" placeholder="Enter amount" type="text" name="amount" class='form-control' value="">
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
          <span class="input-group-addon"><input class="vat_check" type="checkbox"></span>
          <input name="vat" class="form-control" readonly disabled type="text" placeholder="VAT" />

      </div>
    </div>


      <div class="col-xs-3">
        <label>WHT(With Hold Tax)</label>
        <div class="input-group">
            <span class="input-group-addon"><input class="wht_check" type="checkbox"></span>
            <input name="wht" class="form-control" readonly disabled type="text" placeholder="WHT" />
        </div>
      </div>

</div>

<div class="clearfix"></div>

<label>
  Description
</label>

<textarea name="description" class="form-control" rows="2" cols="40">{{old("description")}}</textarea>

<p>&nbsp;</p>
<input type="submit" class="btn btn-primary" value="Save Payment" />
</form>
</div>

@stop
