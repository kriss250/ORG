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
<div class="page-contents">
    <h2>Create Invoice</h2>
    @if(\Session::has("msg"))
    <div class="alert alert-success">
        <button data-toggle="dismiss" class="btn alert-dismiss">
            <i class="fa fa-times"></i>
        </button>
        {{\Session::get("msg")}}
    </div>
    @endif
    <form method="post" action="{{action("InvoiceController@store")}}">
    <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="row">
            <div class="col-xs-5">
                <label>Institution</label>
                <input value="{{$invoice->institution}}" required class="form-control" type="text" name="company" placeholder="Company Name / Individual" />
                <label>
                    Address / Contacts
                </label>
                <textarea name="address" required class="form-control">{{$invoice->address}}</textarea>
            </div>

            <div class="col-xs-5">
                <label>Due Date</label>
                <input value="{{$invoice->due_date}}" autocomplete="off" required name="due_date" type="text" style="display:block;max-width:none;width:100% !important" class="form-control date-picker" />

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
                    <textarea name="desc_{{$i}}" rows="1" class="form-control" placeholder="Description Item {{$i}}">{{$item->description}}</textarea>
                </td>
                <td>
                    <input min="1" name="qty_{{$i}}" type="number" value="{{$item->qty}}" class="form-control" />
                </td>
                <td>
                    <input name="price_{{$i}}" class="form-control" value="{{$item->unit_price}}" type="text" placeholder="Price #" />
                </td>
            </tr>

            <?php $i++; ?>
          @endforeach
        </table>

        <input type="submit" value="Save Invoice" class="btn btn-primary" />
    </form>
</div>

@stop
