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

class TaxController extends Controller
{
    public function store()
    {
        $data = \Request::all();

        if(isset($data['_tax']) && $data['_tax'] > 0)
        {
            //Update
            $tax = \Kris\HR\Models\Tax::find($data['_tax']);
            $tax->tax_name =  $data['name'];
            $tax->type_id = $data['type'];
            $tax->re_occurancy_id = $data['occurancy'];
            $tax->value = $data['value'];

            $tax->save();
            return redirect()->back();
        }else {
            //Create
            $created = \Kris\HR\Models\Tax::create([
                "tax_name"=>$data['name'],
                "value"=>$data['value'],
                "re_occurancy_id"=>$data['occurancy'],
                "type_id"=>$data['type'],
                "deleted"=>0
                ]);
            return $created != null ? redirect()->back()->with("msg","Tax Successfuly created") : redirect()->back()->withErrors(["Tax creating Charge"]);
        }
    }

    public function delete()
    {

    }

    public function edit($id)
    {
        return \View::make("HR::newTax",["tax"=>\Kris\HR\Models\Tax::find($id)]);
    }
}
