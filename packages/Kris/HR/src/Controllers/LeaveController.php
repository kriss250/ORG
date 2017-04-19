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

class LeaveController extends Controller
{
    public function store()
    {
        $data = \Input::all();
        \Kris\HR\Models\EmployeeLeave::create([
            "employee_id"=>$data['employee'],
            "start_date"=>(new  \Carbon\Carbon($data['startdate']))->format("Y-m-d"),
            "end_date"=>(new  \Carbon\Carbon($data['enddate']))->format("Y-m-d"),
            "leave_type_id"=>$data['leave'],
            "description"=>$data['description']
            ]);

        return redirect()->back()->with("msg","Leave saved");
    }

    public function remove($id)
    {
        \Kris\HR\Models\EmployeeLeave::find($id)->delete();
        return redirect()->back();
    }
}
