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

class BankController extends Controller
{
    public function store()
    {
        $data = \Request::all();

        if(isset($data['_bank']) && $data['_bank'] > 0)
        {
            //Update
            $bank = \Kris\HR\Models\Bank::find($data['_bank']);
            $bank->bank_name =  $data['name'];
            $bank->description =$data['description'];
            $bank->address = $data['address'];
            $bank->save();
            return redirect()->back();
        }else {
            //Create
            $created = \Kris\HR\Models\Bank::create([
                "bank_name"=>$data['name'],
                "description"=>$data['description'],
                "address"=>$data['address'],
                "deleted"=>0
                ]);
            return $created != null ? redirect()->back()->with("msg","Bank Successfuly created") : redirect()->back()->withErrors(["Error creating Bank"]);
        }
    }

    public function delete()
    {

    }


    public function edit($id)
    {
        return \View::make("HR::newBank",["bank"=>\Kris\HR\Models\Bank::find($id)]);
    }
}
