@extends(isset($_GET['import']) ? "ORGFrontdesk.Reports.Master" : "Backoffice.Master")


@section("contents")

<?php
$cash=0;
$check=0;
$bank=0;
$cc = 0;
$cr_totals=[];
$cash_o=["amt"=>0,"c"=>null];
$check_o=["amt"=>0,"c"=>null];
$bank_o=["amt"=>0,"c"=>null];
$cc_o = ["amt"=>0,"c"=>null];

$i=1;

$totals = ["cash"=>0,"bank"=>0,"cc"=>0,"check"=>0];
?>
<div class="page-contents">

    <div class="report-filter">
        <table style="width:100%">
            <tr>
                <td>
                    <h3>F.O Payments</h3>
                </td>
                <td>
                    <form style="float:right" action="" class="form-inline" method="get">
                        <label>Date</label>
                        <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control" />
                        -
                        <input name="enddate" type="text" value="{{\ORG\Dates::$RESTODATE }}" class="date-picker form-control" />

                        @if(isset($_GET['import']))
                        <input type="hidden" name="import" value="" />
                        @endif

                        <input type="submit" class="btn btn-success btn-sm" value="Go" />
                        <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="F.O Payments" class="btn btn-default report-print-btn">Print</button>
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

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Guest</th>
                <th>Company</th>
                <th>Room</th>
                <th>Cash</th>
                <th>CC</th>
                <th>Check</th>
                <th>Bank T.</th>
                <th>Comment</th>
                <th>User</th>
                <th>Date</th>
            </tr>
        </thead>
        
        @foreach ($data as $pay)
        <?php

        if(!isset($cr_totals[$pay->cur]))
        {
            $cr_totals[$pay->cur] = 0;
        }

        switch(strtolower($pay->method_name)){
            case "cash" :
                $cash = $pay->credit;
                $cc = 0;
                $bank = 0;
                $check = 0;

                $cash_o=["amt"=>$pay->original_amount,"c"=>$pay->cur];
                $check_o=["amt"=>0,"c"=>null];
                $bank_o=["amt"=>0,"c"=>null];
                $cc_o = ["amt"=>0,"c"=>null];

                $cr_totals[$pay->cur] += $pay->original_amount;
                $totals["cash"] +=$pay->credit;
                break;
            case "credit card" :
                $cash = 0;
                $cc = $pay->credit;
                $bank = 0;
                $check = 0;

                $cash_o=["amt"=>0,"c"=>null];
                $check_o=["amt"=>0,"c"=>null];
                $bank_o=["amt"=>0,"c"=>null];
                $cr_totals[$pay->cur] += $pay->original_amount;
                $cc_o = ["amt"=>$pay->original_amount,"c"=>$pay->cur];

                $totals["cc"] +=$pay->credit;
                break;
            case "check":
                $cash = 0;
                $cc = 0;
                $bank = 0;
                $check = $pay->credit;

                $cash_o=["amt"=>0,"c"=>null];
                $check_o=["amt"=>$pay->original_amount,"c"=>$pay->cur];
                $bank_o=["amt"=>0,"c"=>null];
                $cc_o = ["amt"=>0,"c"=>null];

                $totals["check"] +=$pay->credit;
                $cr_totals[$pay->cur] += $pay->original_amount;
                break;
            case "bank op" :
                $cash = 0;
                $cc = 0;
                $bank = $pay->credit;
                $check = 0;

                $cash_o=["amt"=>0,"c"=>null];
                $check_o=["amt"=>0,"c"=>null];
                $bank_o=["amt"=>$pay->original_amount,"c"=>$pay->cur];
                $cc_o = ["amt"=>0,"c"=>null];
                $totals["bank"] +=$pay->credit;
                $cr_totals[$pay->cur] += $pay->original_amount;
                break;
        }


        ?>
            <tr>
                <td>{{$i}}</td>
                <td>{{$pay->guest}}</td>
                <td>{{$pay->company}}</td>
                <td>{{$pay->room_number}}</td>
                <td>{{$cash}}{{$cash_o["amt"]!=$cash ? " (".$cash_o["c"].$cash_o["amt"].")":""}}</td>
                <td>{{$cc}} {{$cc_o["amt"]!=$cc ? " (".$cc_o["c"].$cc_o["amt"].")":""}}</td>
                <td>{{$check}} {{$check_o["amt"]!=$check ? " (".$check_o["c"].$check_o["amt"].")":""}}</td>
                <td>{{$bank}} {{$bank_o["amt"]!=$bank ? " (".$bank_o["c"].$bank_o["amt"].")":""}}</td>
                <td>{{$pay->comment}}</td>
                <td>{{$pay->username}}</td>
                <td>{{$pay->date}}</td>
            </tr>
        <?php $i++; ?>
        @endforeach
   
        <tfoot>
            <tr>
                <th colspan="4">Total</th>
                <th>{{number_format($totals['cash'])}}</th>
                <th>{{number_format($totals['cc'])}}</th>
                <th>{{number_format($totals['check'])}}</th>
                <th>{{number_format($totals['bank'])}}</th>
                <th class="text-red text-right"><b style="color:darkred">Grand Total:</b></th>
                <th class="text-right" colspan="2">
                    <b style="font-size:13px">{{number_format($totals['cash']+$totals['cc']+$totals['bank']+$totals['check'])}}</b>
                </th>
            </tr>
        </tfoot>
    </table>

    Currencies
    <table class="table table-bordered table-condensed">
        <tr>
            @foreach($cr_totals as $key=>$val)
            <th>{{$key}} {{number_format($val)}}</th>
    @endforeach
        </tr>
    </table>
    <div class="text-center print-footer">
        <table style="margin-bottom:85px;width:100%;" class="table">
            <tr>
                <td>
                    Cashier
                </td>

                <td>
                    CONTROLLER
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

