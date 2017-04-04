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

class DepartmentController extends Controller
{
    public function store()
    {
        $data = \Request::all();

        if(isset($data['_department']) && $data['_department'] > 0)
        {
            //Update
            $dp = \Kris\HR\Models\Department::find($data['_department']);
            $dp->name =  $data['name'];
            $dp->description =$data['description'];
            $dp->save();
            return redirect()->back();
        }else {
            //Create
            $created = \Kris\HR\Models\Department::create(["name"=>$data['name'],"description"=>$data['description'],"deleted"=>0]);
            return $created != null ? redirect()->back()->with("msg","Department Successfuly created") : redirect()->back()->withErrors(["Error creating department"]);
        }
    }

    public function delete()
    {

    }

    public function edit($id)
    {
        return \View::make("HR::newDepartment",["department"=>\Kris\HR\Models\Department::find($id)]);
    }
}
