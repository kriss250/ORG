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

class AbsenceController extends Controller
{
    public function store()
    {
        $data = \Request::all();
        $days = (new \Carbon\Carbon($data['enddate']))->diff(new \Carbon\Carbon($data['startdate']))->d;
        $days = $days==0  ? 1 : $days;
        \Kris\HR\Models\Absence::create([
            "from_date"=>$data['startdate'],
            "to_date"=>$data['enddate'],
            "employee_id"=>$data['employee'],
            "description"=>$data['description'],
            "days"=>$days
            ]);

        return redirect()->back();
    }


    public function remove($id)
    {
        \Kris\HR\Models\Absence::find($id)->delete();
        return redirect()->back();
    }
}
