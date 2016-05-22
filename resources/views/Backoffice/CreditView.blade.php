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

            $.get("{{url('/Backoffice/Debts/export')}}?posdebt=1&id="+id+"&destination="+destination,function(data){
                if (data == "1") {
                    location.reload();
                }else 
                {
                    alert("Error Exporting debt !");
                }
            })
        })
    })
</script>
<div class="page-contents">
    <div class="report-filter">
<table style="width:100%">
    <tr>
        <td><h3>Unexported Debts</h3> </td>
        <td>
           <form style="float:right" action="" class="form-inline" method="get">
                <label>Date</label> 
                <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control">
                -<input name="enddate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control">

                <input type="submit" class="btn btn-success btn-sm" value="Go">
                 <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODATE)) }} - {{ isset($_GET['enddate']) ? $_GET['enddate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODATE)) }}" data-title="Credits" class="btn btn-default report-print-btn">Print</button>
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
    <h3>POS</h3>
    <table class="table table-bordered table-condensed table-striped">
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
                {{number_format($paid) }}
            </td>
            <td colspan="3">

            </td>
        </tr>
    </table>

    <h3>Frontoffice</h3>

    <table class="table table-bordered table-condensed table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>ID</th>
                <th>Guest</th>
                <th>Company</th>
                <th>Amount</th>
                <th>Paid</th>
                <th>Balance</th>
            </tr>
        </thead>
        <?php $i = 1; $amount=0;$fo_paid=0;$fo_dues=0; ?>
        @foreach($fo_data as $fo)
        <tr>
            <td>{{$i}}</td>
            <td>{{$fo->idreservation}}</td>
            <td>{{$fo->guest}}</td>
            <td>{{$fo->name}}</td>
            <td>{{number_format($fo->due_amount)}}</td>
            <td>{{number_format($fo->balance_amount)}}</td>
            <td>{{number_format($fo->dues)}}</td>
        </tr>
        <?php $i++; $amount +=$fo->due_amount; $fo_paid += $fo->balance_amount; $fo_dues += $fo->dues; ?>

        @endforeach

        <tfoot>
            <tr>
                <th colspan="4">TOTAL</th>
                <th>{{number_format($amount)}}</th>
                <th>{{number_format($fo_paid)}}</th>
                <th>{{number_format($fo_dues)}}</th>
            </tr>
        </tfoot>
    </table>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Total Credits</th>
                <th>Total Paid</th>
                <th>Total Dues</th>
            </tr>
            <tr style="font-size:16px">
                <td>
                    {{number_format($amount+$dues)}}
                   
                </td>

                <td>
                    {{number_format($paid+$fo_paid)}}
                </td>

                <td>
                    {{number_format($dues+$fo_dues)}}
                </td>
            </tr>
        </thead>
    </table>
</div>
      
@stop