@extends("Backoffice.Master")

@section("contents")
<div class="page-contents">
    <style>
@media print {
  .summary-block div {
    border:1px solid #000 !important;
  }
}
    </style>


    <div class="report-filter">
        <table style="width:100%">
            <tr>
                <td>
                    <h3>Flash Activity</h3>
                </td>
                <td>
                    <form style="float:right" action="" class="form-inline" method="get">
                        <label>Date</label>
                        <input name="startdate" type="text" value="{{\ORG\Dates::$RESTODATE }}" class="date-picker form-control" />-
                        <input name="enddate" type="text" value="{{\ORG\Dates::$RESTODATE }}" class="date-picker form-control" />

                        <input type="submit" class="btn btn-success btn-sm" value="Go" />
                        <button type="button" data-dates="{{isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{isset($_GET['enddate']) ? $_GET['enddate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Flash Activity" class="btn btn-default report-print-btn">Print</button>
                    </form>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="text-danger">
                        <b>Date : {{isset($_GET['startdate']) && isset($_GET['enddate'])  ?  \App\FX::Date($_GET['startdate'])." - ".\App\FX::Date($_GET['enddate']) : \App\FX::Date(\ORG\Dates::$RESTODATE) }}</b>
                    </p>
                </td>
            </tr>
        </table>


    </div>



    <style>
        .summary-block p {
            font-size: 18px;
            font-weight: bold;
            margin-top: 8px;
        }

        .summary-block {
            padding: 10px 30px;
        }

            .summary-block p sup {
                font-weight: normal;
            }

            .summary-block div {
                border: 1px solid rgb(200,200,200);
                padding: 8px 15px;
                margin-right: -1px;
                font-size: 16px;
                background: rgb(250,250,250);
            }
    </style>
    <p class="text-center">
        <h3 class="text-center">Summary</h3>
        <span style="display:block" class="text-center">Sales</span>
    </p>


    <div class="row summary-block">
        <div class="col-xs-3">
            Frontdesk turnover
            <p>
                {{number_format($fo_turnover)}}
                <sup class="text-{{$fo_turnover_rate  > 0 ? " success" :'danger'}}">{{$fo_turnover_rate > 0 ? "+" : "-"}}{{number_format($fo_turnover_rate,1)}}%</sup>
            </p>
        </div>

        <div class="col-xs-3">
            POS turnover
            <p>
                {{number_format($pos_turnover)}}
                <sup class="text-{{$pos_turnover_rate > 0 ? " success" : "danger"}}">{{$pos_turnover_rate > 0 ? "+" : ""}}{{number_format($pos_turnover_rate,1)}}%</sup>
            </p>
        </div>
        <div class="col-xs-3">
            Expenses
            @foreach(\App\Cashbook::getCashExpenses() as $exp)
            <p>
                {{    number_format($exp->amount)}}
                <span style="font-size:12px;opacity:.6">{{    $exp->cashbook_name}}</span>
            </p>
            @endforeach

        </div>

        <div class="col-xs-3">
            Average Room Rate
            <p>
                {{number_format($avg_rate)}}
                <sup class="text-{{$avg_rate_rate  > 0 ? " success" :'danger'}}">{{$avg_rate_rate > 0 ? "+" : ""}}{{number_format($avg_rate_rate,1)}}%</sup>
            </p>
        </div>
    </div>
    <hr />
    <h3>Overall Turnover & Payments</h3>
    <span style="margin-top:-10px;display:block;margin-bottom:15px">Total sales and payments</span>

    <table class="table table-striped table-bordered table-condensed">

        <?php $st_header = ""; $st_td = "<td><b>Frontdesk</b> : ".number_format($fo_turnover)."</td>"; $counter = 0;$total_pos_amount= 0; $stores_count = count($pos_stores["stores"]); ?>

        @foreach(\App\Store::all() as $store)
        <?php
 $st_header .= "<th>{$store->store_name}</th>";
            foreach($pos_stores['sales'] as $sl)
            {
                if($sl->store_name==$store->store_name)
                {
                    $st_td .= "<td><b>{$store->store_name}</b> : ".number_format($sl->amount)."</td>";
                    $total_pos_amount += $sl->amount;
                }
            }
        ?>

        @endforeach

        <tr style="font-size:18px;">
            {!!$st_td !!}
        </tr>
        <tr>
            <td colspan="{{$stores_count+1}}" class="text-center">
                <h4>GT : {{number_format($total_pos_amount+$fo_turnover)}}</h4>
            </td>
        </tr>

        <tr>
            <th>
                Cash
            </th>

            <th>
                CC
            </th>
            <th>
                Check
            </th>
            <th>
                Bank
            </th>
        </tr>
        <tr>
            <td>
                0
            </td>

            <td>
                0
            </td>
            <td>
                0
            </td>
            <td>
                0
            </td>
        </tr>

        <tr>
            <td class="text-center" style="font-weight:bold;font-size:16px !important" colspan="{{$stores_count+1}}">
                GT P : 0
            </td>
        </tr>

        <tr>
            <th>DEBTORS</th>
        </tr>

        <tr>
            <td>Current DEBIT</td>
            <td>0</td>
            <td>Cumultative Debit</td>
        </tr>
        <tr>
            <th>CREDITORS</th>
        </tr>

        <tr>
            <td>Current CREDIT</td>
            <td>0</td>
            <td>Cumultative Credit</td>
        </tr>
    </table>


    <div class="text-center print-footer">
        <table style="margin-bottom:85px;width:100%;" class="table">
            <tr>
                <td>
                    {{\Auth::user()->username}}
                </td>

                <td>
                    C.F.O.O
                </td>

                <td>
                    ACCOUNTANT
                </td>

                <td>
                    DAF
                </td>

                <td>
                    G. MANAGER
                </td>
            </tr>
        </table>
        <div class="clearfix"></div>
    </div>
</div>


@stop
