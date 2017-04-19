@extends("Frontdesk::MasterIframe")

@section("contents")
<div class="report-filter">
    <form action="" method="get">
        <div style="width:100%;max-width:980px;margin:auto" class="row">
            <div class="col-xs-5">
                <h4>HR Reports</h4>
            </div>

            <div class="col-xs-7 container-fluid text-right">

                <div class="col-xs-4">
                   
                    <input style="max-width:100%" name="startdate" type="text" value="{{\Kris\Frontdesk\Env::WD()->format("Y-m-d")}}" class="datepicker form-control" />-
                </div>
                <div class="col-xs-4">
                    <input name="enddate" type="text" value="{{\Kris\Frontdesk\Env::WD()->format("Y-m-d")}}" class="datepicker form-control" />
                </div>

                <div class="col-xs-3">  
                    <input type="submit" class="btn btn-success btn-sm" value="Go" />
                    <button type="button" onclick="window.print()" class="btn btn-default report-print-btn">Print</button>
                </div>
            </div>

        </div>
    </form>
</div>
<div class="print-document">
    @include("HR::reports.report-print-header")
    <p class="report-title">Payroll Report</p>

    <?php $x = 1; $total = 0; ?>

    @foreach(\Kris\HR\Models\Bank::all() as $bank)
    <h3>{{$bank->bank_name}}</h3>
    <?php $stotal = 0; ?>
    <table class="table table-bordered table-condensed table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Name</th>
                <th>Account</th>
                <th>Net Amount</th>
            </tr>
        </thead>
       
        @foreach(\Kris\HR\Models\PayrollEmployee::join("bank_accounts","idbank_accounts","=","bank_account_id")->where("bank_id",$bank->idbanks)->get() as $pay)
        <?php if($pay->bank_account_id<1) continue;  $account = \Kris\HR\Models\BankAccount::find($pay->bank_account_id);
              $_advance = \Kris\HR\Models\Advance::select(\DB::raw("sum(amount) as amount"))->where("employee_id",$account->employee->idemployees)->where(\DB::raw("date(date)"),\Carbon\Carbon::now()->format("Y-m-d"))->groupBy("employee_id")->first();
              $advance = is_null($_advance) ?  0 : $_advance->amount;

        ?>
            <tr>
                <td>{{$x}}</td>
                <td>{{$account->employee->idemployees}}</td>
                <td>{{$account->employee->firstname}} {{$account->employee->lastname}}</td>
                <td>{{$account->account_name}}</td>
                <td>{{number_format($pay->net_salary-$advance)}}</td>
                
            </tr>
        <?php $stotal += $pay->net_salary;$x++; ?>
        @endforeach
        <tfoot>
            <tr>
                <th>
                    S / TOTAL
                </th>
                <th class="text-right" colspan="4">{{number_format($stotal)}}</th>
            </tr>
        </tfoot>
    </table>
    <?php $total += $stotal;  ?>
    @endforeach
   <h3>TOTAL {{number_format($total)}}</h3>
    <br />
    @include("HR::reports.report-footer")

</div>

@stop