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

class EmployeeController extends Controller
{
    public function store()
    {
        $data = \Request::all();

        if(isset($data['_employee']) && $data['_employee'] > 0)
        {
            //Update
            $emp = \Kris\HR\Models\Employee::find($data['_employee']);
            $emp->firstname=$data['firstname'];
            $emp->lastname=$data['lastname'];
            $emp->middlename=$data['mname'];
            $emp->father_name=$data['father'];
            $emp->mother_name=$data['mother'];
            $emp->id_passport=$data['id_passport'];
            $emp->nationality=$data['country'];
            $emp->department_id=$data['department'];
            $emp->post_id=$data['post'];
            $emp->birthdate=$data['birthdate'];
            $emp->birth_place=$data['birth_place'];
            $emp->gender=$data['gender'];
            $emp->hire_date=$data['hire_date'];
            $emp->description=$data['description'];
            $emp->highest_degree =$data['degree'] ;
            $address = \Kris\HR\Models\Address::where("employee_id",$emp->idemployees)->first();
            $address->country = $data['country'];
            $address->city = $data['city'];
            $address->save();
            $emp->save();

            if($emp->salary == null || !isset($emp->salary{count($emp->salary)-1}) || $data['salary']!=$emp->salary{count($emp->salary)-1}->amount)
            {
                //Create new Salary the salary
                \Kris\HR\Models\Salary::create([
                "employee_id"=>$emp->idemployees,
                "amount"=>$data['salary']
                ]);
            }
            return redirect()->back();

        }else {
            //Create
            $emp = \Kris\HR\Models\Employee::create([
                    "firstname"=>$data['firstname'],
                    "lastname"=>$data['lastname'],
                    "middlename"=>$data['mname'],
                    "father_name"=>$data['father'],
                    "mother_name"=>$data['mother'],
                    "id_passport"=>$data['id_passport'],
                    "nationality"=>$data['country'],
                    "department_id"=>$data['department'],
                    "post_id"=>$data['post'],
                    "birthdate"=>$data['birthdate'],
                    "birth_place"=>$data['birth_place'],
                    "gender"=>$data['gender'],
                    "hire_date"=>$data['hire_date'],
                    "description"=>$data['description'],
                    "highest_degree"=>$data['degree'],
                    "deleted"=>0
                ]);

            \Kris\HR\Models\Salary::create([
                "employee_id"=>$emp->idemployees,
                "amount"=>$data['salary']
                ]);

             \Kris\HR\Models\Contact::create([
                "employee_id"=>$emp->idemployees,
                "phone1"=>$data['phone'],
                "email1"=>$data['email']
                ]);

            $address = \Kris\HR\Models\Address::create([
                "employee_id"=>$emp->idemployees,
                "country"=>$data['country'],
                "city"=>$data['city']
                ]);
            return $emp != null && $address != null ? redirect()->back()->with("msg","Bank Successfuly created") : redirect()->back()->withErrors(["Error creating Bank"]);
        }
    }

    public function delete()
    {

    }

    public function edit($id)
    {
        return \View::make("HR::newEmployee",["employee"=>\Kris\HR\Models\Employee::find($id)]);
    }

    public function show($id)
    {
        return \View::make("HR::employeeViewer",["employee"=>\Kris\HR\Models\Employee::find($id)]);
    }
}
