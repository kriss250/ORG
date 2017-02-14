@extends('Backoffice.Master')

@section("contents")
<script>
    function confirmDeletion(src,e)
    {
        e.preventDefault();
        if (!confirm("Are you sure you want to delete this invoice ?")) return;
        location.href = $(src).attr("href");
    }
</script>
<div class="page-contents">
    <h2>Saved Invoices</h2>
    <p>Search Invoice</p>
    <form class="form-inline" action="" method="get">
      <input class="form-control" name="invoice" placeholder="Invoice Number # 12 or 12/2016" />
      <input type="submit" value="Search" class="btn btn-danger" />
    </form>
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
            <?php

            $invoices = \App\Invoice::select(\DB::raw("idinvoices,invoices.created_at,institution,invoices.description,due_date,username,sum(unit_price*qty*days) as invoice_total"))
              ->leftJoin("invoice_items","invoice_items.invoice_id","=","idinvoices")
              ->join("org_pos.users","user_id","=","users.id")
              ->groupBy("idinvoices")
              ->orderBy("created_at","desc")
              ->limit("100");

              if(isset($_GET['invoice']) && $_GET['invoice'] !== "")
              {
                $id = strpos($_GET['invoice'],"/") > 0 ? explode("/", $_GET['invoice'])[0] : $_GET['invoice'];
                $invoices = $invoices->where('code', $id)->get();
              }else {
                $invoices = $invoices->get();
              }

              ?>
            @foreach($invoices as $invoice)
            <tr>
                <td>{{$invoice->code < 10 ? "00".$invoice->code : $invoice->code }}/{{ (new \Carbon\Carbon($invoice->created_at))->format("Y")}}</td>
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
                    <button onclick="window.open('{{action("InvoiceController@show",$invoice->idinvoices)}}','_blank','fullscreen=yes,width=990,height=620')" class="btn btn-xs"><i class="fa fa-eye"></i></button>
                    <a href="{{action('InvoiceController@edit',$invoice->idinvoices)}}" class="btn btn-xs">
                        <i class="fa fa-pencil"></i>
                    </a>
                    <a onclick="confirmDeletion(this,event);" href="{{action('InvoiceController@delete',$invoice->idinvoices)}}" class="btn btn-xs btn-danger">
                        <i class="fa fa-trash"></i>
                    </a>
                </td>
            </tr>
            @endforeach

        </table>

</div>

@stop
