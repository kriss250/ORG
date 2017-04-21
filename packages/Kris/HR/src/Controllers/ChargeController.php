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

class ChargeController extends Controller
{
    public function store()
    {
        $data = \Request::all();

        if(isset($data['_charge']) && $data['_charge'] > 0)
        {
            //Update
            $charge = \Kris\HR\Models\Charge::find($data['_charge']);
            $charge->charge_name =  $data['name'];
            $charge->description =$data['description'];
            $charge->charge_type = $data['type'];
            $charge->re_occurancy_id = $data['occurancy'];
            $charge->value = $data['value'];

            $charge->save();
            return redirect()->back();
        }else {
            //Create
            $created = \Kris\HR\Models\Charge::create([
                "charge_name"=>$data['name'],
                "description"=>$data['description'],
                "value"=>$data['value'],
                "re_occurancy_id"=>$data['occurancy'],
                "charge_type"=>$data['type'],
                "deleted"=>0
                ]);
            return $created != null ? redirect()->back()->with("msg","Charge Successfuly created") : redirect()->back()->withErrors(["Error creating Charge"]);
        }
    }

    public function delete()
    {

    }

    public function edit($id)
    {
        return \View::make("HR::newCharge",["charge"=>\Kris\HR\Models\Charge::find($id)]);
    }


    public function saveEmployeeCharge()
    {
        $data = \Request::all();

        if(isset($data['charge'])){
            \Kris\HR\Models\EmployeeCharge::find($data['charge'])->update([
                "employee_id"=>$data['employee'],
                "amount"=>$data['amount'],
                "date"=>$data['date'],
                "description"=>$data['description']
                ]);
        }else {
            \Kris\HR\Models\EmployeeCharge::create([
                "employee_id"=>$data['employee'],
                "amount"=>$data['amount'],
                "date"=>$data['date'],
                "description"=>$data['description']
                ]);

        }
        return redirect()->back();
    }


    public function removeEmpCharge($id)
    {
        \Kris\HR\Models\EmployeeCharge::find($id)->update(["deleted"=>"1"]);

        return redirect()->back();
    }

    public function editEmpCharge($id)
    {
        return \View::make("HR::addEmployeeCharge")->with(["charge"=>\Kris\HR\Models\EmployeeCharge::find($id)]);
    }
}
