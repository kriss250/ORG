@extends('Backoffice.Master')

@section("contents")
<style>
    .invoice-items-table .form-control {
        border-color: transparent;
        height: 28px;
        font-size: 13px;
    }

    .invoice-items-table textarea.form-control {
        height:27px;
        resize:none;
        font-size: 13px;
    }
</style>


<script>
  var addRow = function (ev)
  {
    ev.preventDefault();
    var table = $(".invoice-items-table");
    var currentRows  = $(".invoice-items-table tbody").children("tr").length;
    var nextNo = currentRows+1;

    var row = $("<tr>");
    var col = $("<td>");

    var dateInput = $('<input name="date_'+nextNo+'" class="form-control date-picker" type="text" placeholder="Y-m-d" />');
    var descInput = $('<input type="text" data-table="org_backoffice.invoice_items" data-field="description" name="desc_'+nextNo+'" rows="1" class="form-control suggest-input" placeholder="Description Item '+nextNo+'" />');
    var qtyInput = $('<input required min="1" name="qty_'+nextNo+'" type="number" class="form-control" />');
    var upInput = $('<input required name="price_'+nextNo+'" class="form-control" type="text" placeholder="Price #" />');
    var daysInput = $('<input required min="1" value="1" name="days_'+nextNo+'" type="number" class="form-control" />')
    $(row)
    .append($("<td>").html(dateInput))
    .append($("<td>").html(descInput))
    .append($("<td>").html(daysInput))
    .append($("<td>").html(qtyInput))
    .append($("<td>").html(upInput));

  $(table).append(row);

  }
</script>
<div class="page-contents">
    <h2>Edit Invoice</h2>

    <form method="post" action="{{action("InvoiceController@update",$invoice->idinvoices)}}">
      <input type="hidden" name="_method" value="put" />
    <input type="hidden" value="{{csrf_token()}}" name="_token" />

        <div class="row">
            <div class="col-xs-5">
                <label>Institution</label>
                <input value="{{$invoice->institution}}" required class="form-control" type="text" name="company" placeholder="Company Name / Individual" />
                <label>
                    Address / Contacts
                </label>
                <textarea  name="address" rows="4" required class="form-control">{{$invoice->address}}</textarea>
            </div>

            <div class="col-xs-5">
                <label>Due Date</label>
                <input autocomplete="off" value="{{$invoice->due_date}}" required name="due_date" type="text" style="display:block;max-width:none;width:100% !important" class="form-control date-picker" />
                <label>Delivery Date</label>
                <input autocomplete="off" value="{{$invoice->sent_date}}" required name="delivery_date" type="text" style="display:block;max-width:none;width:100% !important" class="form-control date-picker" />

                <label>Prestation / Description</label>
                <input value="{{$invoice->description}}" autocomplete="off" required name="description" type="text" style="display:block;max-width:none;width:100% !important" class="form-control" />

            </div>
        </div>
        <hr />
        <label>Invoice Items</label>
        <table class="invoice-items-table table-bordered table table-condensed table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th style="width:50%">Description</th>
                    <th>
                      Days
                    </th>
                    <th width="10%">Qty</th>
                    <th>Unit Price</th>
                </tr>
            </thead>
            <?php $i=1; ?>
            @foreach($invoice->items as $item)
            <tr>
                <td>
                    <input value="{{$item->date}}" name="date_{{$i}}" class="form-control date-picker" type="text" placeholder="Y-m-d" />
                </td>
                <td>
                    <input value="{{$item->description}}" required type="text" data-table="org_backoffice.invoice_items" data-field="description" name="desc_{{$i}}" rows="1" class="form-control suggest-input" placeholder="Description Item {{$i}}" />
                </td>

                <td>
                  <input required min="1" value="{{$item->days}}" name="days_{{$i}}" type="number" class="form-control" />
                </td>
                <td>
                  <input required min="1" value="{{$item->qty}}" name="qty_{{$i}}" type="number" class="form-control" />
                </td>
                <td>
                    <input required value="{{$item->unit_price}}" name="price_{{$i}}" class="form-control" type="text" placeholder="Price #" />
                </td>
            </tr>

            <?php $i++; ?>
          @endforeach
        </table>
        <button onclick="addRow(event)" style="margin-top:-10px;display:block;margin-bottom:20px;" class="btn btn-success btn-xs"><i class="fa fa-plus"></i> Add row</button>
        <input type="submit" value="Save Invoice" class="btn btn-primary" />
    </form>
</div>

@stop
