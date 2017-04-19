<?php

/**
 * OperationsController short summary.
 *
 * OperationsController description.
 *
 * @version 1.0
 * @author kris
 */
namespace Kris\HR\Controllers;
use App\Http\Controllers\Controller;

class PayrollController extends Controller
{
    public function store()
    {
        $data = \Request::all();
        $charges = [];
        $taxes =  [];
        $_charges  = [];
        $_taxes = [];
        $pay = \Kris\HR\Models\Payroll::create([
            "month"=>$data['month'],
            "year"=>$data['year'],
            "description"=>$data['description']
            ]);

        $keys = array_keys($data);

        foreach($keys as $key)
        {
            $parts = explode("_",$key);
            if($parts > 1)
            {
                if($parts[0]=="tax")
                {
                    $taxes[] = ["tax_id"=>$data[$key],"payroll_id"=>$pay->idpayroll];
                    $_taxes[] = \Kris\HR\Models\Tax::find($data[$key]);
                }else if($parts[0]=="charge")
                {
                    $charges[] =  ["charge_id"=>$data[$key],"payroll_id"=>$pay->idpayroll];
                    $_charges[] = \Kris\HR\Models\Charge::find($data[$key]);
                }
            }
        }
        $emps  = \Kris\HR\Models\Employee::join("employee_contract","employee_id","=","idemployees")->where("active","1")->whereNull("termination_date")->get();


        \Kris\HR\Models\PayrollCharge::insert($charges);

        \Kris\HR\Models\PayrollTax::insert($taxes);

        foreach($emps as $emp)
        {
            $brute = $emp->salary[count($emp->salary)-1]->amount;
            $net = $brute;
            foreach($_charges as $ch)
            {

                $net -= $ch->charge_type == \Kris\HR\Models\Type::FIXED ? $ch->value  : $ch->value*$brute/100;
            }

            foreach($_taxes as $tx)
            {
                $net -= $tx->tax_type == \Kris\HR\Models\Type::FIXED ? $tx->value  : $tx->value*$brute/100;
            }

            $account = \Kris\HR\Models\BankAccount::where("active","1")->where("employee_id",$emp->idemployees)->first();
            $acc_name = $account == null ? null : $account->account_name;
            $acc_id = $account ==null ? "0" : $account->idbank_accounts;

            \Kris\HR\Models\PayrollEmployee::create([
                "employee_id"=>$emp->idemployees,
                "payroll_id"=>$pay->idpayroll,
                "net_salary"=>$net,
                "bank_account"=>$acc_name,
                "bank_account_id"=> $acc_id
                ]);
        }


        return redirect()->back();

    }


    public function remove($id)
    {
        \Kris\HR\Models\PayrollCharge::find($id)->delete();
        \Kris\HR\Models\PayrollTax::find($id)->delete();
        \Kris\HR\Models\PayrollEmployee::find($id)->delete();
        \Kris\HR\Models\Payroll::find($id)->delete();

        return redirect()->back();
    }
}
