@extends('Backoffice.Master')

@section("contents")
<script>
    $(function () {
        $(".export-btn").click(function (e) {
            e.preventDefault();

            var selector = $(this).attr("data-selector");
            var destination = $(selector).val();
            var id = $(this).attr('data-id');

            if ($(selector).val().toLowerCase() == "choose")
            {
                alert("Please choose destination");
                return false;
            }

            $.get("{{url('/Backoffice/Debts/export')}}?posdebt=0&id="+id+"&destination="+destination,function(data){
                if (data == "1") {
                    location.reload();
                }else 
                {
                    alert("Error updating debt !");
                }
            })
        })
    })
</script>
<div class="page-contents">
    <div class="report-filter">
<table style="width:100%">
    <tr>
        <td><h3>External Debts</h3> </td>
        <td>
           <form style="float:right" action="" class="form-inline" method="get">
                <label>Date</label> 
                    <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODT }}" class="date-picker form-control">
                -<input name="enddate" type="text" value="{{ \ORG\Dates::$RESTODT }}" class="date-picker form-control">


                <input type="submit" class="btn btn-success btn-sm" value="Go">
                 <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['enddate']) ? $_GET['enddate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="External Credits" class="btn btn-default report-print-btn">Print</button>
           </form> 
        </td>
    </tr>

        <tr>
      <td>
      <p class="text-danger"><b>Date : {{ isset($_GET['startdate']) && isset($_GET['enddate'])  ?  \App\FX::Date($_GET['startdate'])." - ".\App\FX::Date($_GET['enddate']) : \App\FX::Date(\ORG\Dates::$RESTODATE) }}</b></p>
      </td>
    </tr>

</table>
</div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Paid Amount</th>
                <th>Cashier</th>
                <th>Date</th>
                <th class="hidden-pr">Export</th>
            </tr>
        </thead>
        <?php
        $dues=0;$paid = 0;
        $i=1;
        ?>
    @foreach($data as $bill)
        <tr>
            <td>{{ $bill->idbills }}</td>
            <td>{{ $bill->customer }}</td>
            <td>{{ $bill->bill_total }}</td>
            <td>{{ $bill->amount_paid }}</td>
            <td>{{ $bill->username}}</td>
            <td>{{ \App\FX::DT($bill->date) }}</td>
            <td class="hidden-pr">
                <select id="exp_type_{{$i}}">
                    <option>Choose</option>
                    <option>Internal</option>
                    <option>External</option>
                </select>
                <button data-id="{{ $bill->idbills }}" data-selector="#exp_type_{{$i}}" class="btn export-btn btn-xs btn-danger">
                    <i class="fa fa-exchange"></i>
                </button>
            </td>
            <?php 
                    $dues += $bill->bill_total;
                  $paid += $bill->amount_paid;
                  $i++;
            ?>
        </tr>
    @endforeach
        <tr style="font-weight:bold">
            <td colspan="2s">
                TOTAL
            </td>
            <td>
                {{ number_format($dues) }}
            </td>
            <td>
                {{ number_format($paid) }}
            </td>
            <td colspan="3">

            </td>
        </tr>
    </table>
</div>
      
@stop