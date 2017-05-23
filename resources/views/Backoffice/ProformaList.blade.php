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

            $proformas = \App\Proforma::select(\DB::raw("idproforma,proforma.created_at,institution,proforma.description,due_date,username,sum(unit_price*qty*days) as invoice_total,code"))
              ->leftJoin("proforma_items","proforma_items.proforma_id","=","idproforma")
              ->join("org_pos.users","user_id","=","users.id")
              ->groupBy("idproforma")
              ->orderBy("created_at","desc")
              ->limit("100");

              if(isset($_GET['invoice']) && $_GET['invoice'] !== "")
              {
                $id = strpos($_GET['invoice'],"/") > 0 ? explode("/", $_GET['invoice'])[0] : $_GET['invoice'];
                $proformas = $proformas->where('code', $id)->get();
              }else {
                $proformas = $proformas->get();
              }

              ?>
            @foreach($proformas as $proforma)
            <tr>
                <td>{{$proforma->code < 10 ? "00".$proforma->code : $proforma->code }}/{{ (new \Carbon\Carbon($proforma->created_at))->format("Y")}}</td>
                <td>{{$proforma->created_at}}</td>
                <td>{{$proforma->institution}}</td>
                <td>{{$proforma->description}}</td>
                <td>{{$proforma->due_date}}</td>
                <td widtd="18%">{{$proforma->invoice_total}}</td>
                <td>{{$proforma->username}}</td>
                <td>
                    <button onclick="window.open('{{action("ProformaController@show",$proforma->idproforma)}}','_blank','fullscreen=yes,width=990,height=620')" class="btn btn-xs"><i class="fa fa-eye"></i></button>
                    <a href="{{action('ProformaController@edit',$proforma->idproforma)}}" class="btn btn-xs">
                        <i class="fa fa-pencil"></i>
                    </a>
                    <a onclick="confirmDeletion(this,event);" href="{{action('ProformaController@delete',$proforma->idproforma)}}" class="btn btn-xs btn-danger">
                        <i class="fa fa-trash"></i>
                    </a>
                </td>
            </tr>
            @endforeach

        </table>

</div>

@stop
