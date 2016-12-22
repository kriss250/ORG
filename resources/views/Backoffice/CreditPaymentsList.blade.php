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
    <h2>Payments</h2> 
    <h5>For {{$credit->creditor->name}} on Voucher {{$credit->voucher}}</h5>


        <hr />
        <label>Items</label>
       
    <table class="table table-striped table-bordered">
        <tr>
            <th>
                Amount
            </th>
            <th>
                Description
            </th>
            <th>
                Date
            </th>
            <th>
                <i class="fa fa-trash"></i>
            </th>
        </tr>
        @foreach($pays as $pay)
        <tr>
            <td>
                {{number_format($pay->amount)}}
            </td>
            <td>
                {{$pay->description}}
            </td>
            <td>{{$pay->created_at}}</td>
            <td>
                <a class="btn btn-danger btn-xs" href="{{action("CreditsController@deletePayment",$pay->idpayment)}}" onclick="return confirm('Are you sure you want to delete this payment ?');">
                    <i class="fa fa-trash"></i>
                </a>
            </td>
        </tr>
        @endforeach
    </table>

</div>

@stop
