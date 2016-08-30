@extends('Backoffice.Master')

@section("contents")

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

    <input type="hidden" value="{{csrf_token()}}" name="_token" />

        <hr />
        <label>Invoice Items</label>
        <table class="invoice-items-table table-bordered table table-condensed table-striped">
            <thead>
                <tr>
                    <th>N<sup>0</sup></th>
                    <th>Date</th>
                    <th>Company</th>
                    <th>Description</th>
                    <th>Due Date</th>
                    <th width="18%">Amount</th>
                    <th>User</th>
                    <th>Action</th>
                </tr>
            </thead>

            @foreach(\App\Invoice::select(\DB::raw("idinvoices,invoices.created_at,institution,invoices.description,due_date,username,sum(unit_price*qty) as invoice_total"))
              ->leftJoin("invoice_items","invoice_items.invoice_id","=","idinvoices")
              ->join("org_pos.users","user_id","=","users.id")
              ->groupBy("idinvoices")
              ->get() as $invoice)
            <tr>
                <td>{{$invoice->idinvoices < 10 ? "00".$invoice->idinvoices : $invoice->idinvoices }}/{{ (new \Carbon\Carbon($invoice->created_at))->format("Y")}}</td>
                <td>{{$invoice->created_at}}</td>
                <td>{{$invoice->institution}}</td>
                <td>{{$invoice->description}}</td>
                <td>{{$invoice->due_date}}</td>
                <td widtd="18%">{{$invoice->invoice_total}}</td>
                <td>{{$invoice->username}}</td>
                <td>
                  <a href="{{action('InvoiceController@showPayments',$invoice->idinvoices)}}" class="btn btn-xs">
                      <i class="fa fa-money"></i>
                  </a>
                    <button onclick="window.open('{{action("InvoiceController@show",$invoice->idinvoices)}}','_blank')" class="btn btn-xs"><i class="fa fa-eye"></i></button>
                    <a href="{{action('InvoiceController@edit',$invoice->idinvoices)}}" class="btn btn-xs">
                        <i class="fa fa-pencil"></i>
                    </a>
                    <a href="{{action('InvoiceController@delete',$invoice->idinvoices)}}" class="btn btn-xs btn-danger">
                        <i class="fa fa-trash"></i>
                    </a>
                </td>
            </tr>
            @endforeach

        </table>

</div>

@stop
