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

class AdvanceController extends Controller
{
    public function store()
    {
        $data = \Request::all();
        \Kris\HR\Models\Advance::create([
            "amount"=>$data['amount'],
            "description"=>$data['description'],
            "employee_id"=>$data['employee'],
            "date"=>\Carbon\Carbon::now()->format("Y-m-d H:i:s"),
            "deleted"=>"0"
            ]);

        return redirect()->back();
    }


    public function remove($id)
    {
        \Kris\HR\Models\Advance::find($id)->update([
            "deleted"=>"1"
            ]);

        return redirect()->back();
    }
}
