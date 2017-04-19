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
    <p class="report-title">Payroll Report (Detailed)</p>

    <?php $x = 1; $total = 0; $pay = \Kris\HR\Models\Payroll::first();
    $charges =  \Kris\HR\Models\PayrollCharge::where("payroll_id",$pay->idpayroll)->get();
    $taxes =  \Kris\HR\Models\PayrollTax::where("payroll_id",$pay->idpayroll)->get();
$ch_th ="";
    foreach($charges as $c)
    {
        $ch_th .="<th>".$c->charge->charge_name."</th>";
    }

    foreach($taxes as $t)
    {
        $ch_th .="<th>".$t->tax->tax_name."</th>";
    }

    $tfoot_charges = [];
                 $tfoot_taxes = [];
    ?>
     <table class="table table-bordered table-condensed table-striped">
         <thead>
             <tr>
                 <th>Name</th>
                 <th>Departmnet</th>
                 <th>Post</th>
                 <th>Salary</th>
                 <th>Advance</th>
                 {!!$ch_th!!}
                 <th>Net Salary</th>
             </tr>
         </thead>
    @foreach(\Kris\HR\Models\PayrollEmployee::where("payroll_id",$pay->idpayroll)->get() as $py)
         <?php $py->employee->load("department"); $py->employee->load("post");$py->employee->load("salary");
         $sls = $py->employee->salary; $current_salary = $sls[count($sls)-1];
            $current_salary = $sls[count($sls)-1];
          $_advance = \Kris\HR\Models\Advance::select(\DB::raw("sum(amount) as amount"))->where("employee_id",$py->employee->idemployees)->where(\DB::raw("date(date)"),\Carbon\Carbon::now()->format("Y-m-d"))->groupBy("employee_id")->first();
          $advance = is_null($_advance) ?  0 : $_advance->amount;
          $net_salary = $current_salary->amount;
          
         ?>
        <tr>
            <td>{{$py->employee->firstname}} {{$py->employee->lastname }}</td>
            <td>{{$py->employee->department->name}}</td>
            <td>{{$py->employee->post->name}}</td>
            <td>{{number_format($current_salary->amount)}}</td>
            <td>
                {{number_format($advance)}}
            </td>

            <?php
                 
                foreach($charges as $c)
                {
                    $cval =$c->charge->charge_type==\Kris\HR\Models\Type::FIXED? $c->charge->value : $current_salary->amount*$c->charge->value /100;
                    $net_salary -= $cval;

                    if(array_key_exists($c->charge->idcharges,$tfoot_charges))
                    {
                        $tfoot_charges[$c->charge->idcharges] += $cval;

                    }else {
                         $tfoot_charges[$c->charge->idcharges] = $cval;
                    }

                    echo  "<td>{$cval}</td>";
                }

                
                foreach($taxes as $t)
                {

                    $tval = $t->tax->tax_type==\Kris\HR\Models\Type::FIXED? $t->tax->value : $current_salary->amount*$t->tax->value /100 ;
                    $net_salary -= $tval;

                     if(array_key_exists($t->tax->idtaxes,$tfoot_taxes))
                    {
                        $tfoot_taxes[$t->tax->idtaxes] += $tval;

                    }else {
                         $tfoot_taxes[$t->tax->idtaxes] = $tval;
                    }

                    echo "<td>{$tval}</td>";
                }

                $total += $net_salary;
            ?>
            <td>
                {{number_format($net_salary)}}
            </td>

        </tr>

      
    @endforeach
       
         <tfoot>
             <tr>
                 <th colspan="5">TOTAL</th>
                 

                 @foreach($tfoot_charges as $tc)
                 <th>
                     {{number_format($tc)}}
                 </th>
                 @endforeach

                 @foreach($tfoot_taxes as $tt)
                 <th>
                     {{number_format($tt)}}
                 </th>
                 @endforeach

                 <th>
                     {{number_format($total)}}
                 </th>
             </tr>
         </tfoot>
    </table>
   
    <br />
    @include("HR::reports.report-footer")

</div>

@stop