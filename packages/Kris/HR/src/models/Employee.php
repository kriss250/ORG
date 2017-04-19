<?php

/**
 * Room short summary.
 *
 * Room description.
 *
 * @version 1.0
 * @author kris
 */
namespace Kris\HR\Models;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $connection = "mysql_hr";
    protected $table = "employees";
    public $primaryKey = "idemployees";
    public $guarded = [];

    public function address()
    {
        return $this->hasMany("\Kris\HR\Models\Address","employee_id","idemployees");
    }

    public function contact()
    {
        return $this->hasMany("\Kris\HR\Models\Contact","employee_id","idemployees");
    }

    public function department()
    {
        return $this->hasOne("\Kris\HR\Models\Department","iddepartments","department_id");
    }

    public function post()
    {
        return $this->hasOne("\Kris\HR\Models\Post","idposts","post_id");
    }

    public function salary()
    {
        return $this->hasMany("\Kris\HR\Models\Salary","employee_id","idemployees");
    }

    public function contract()
    {
        return $this->hasMany("\Kris\HR\Models\EmployeeContract","idemployees","employee_id");
    }

    public function getCurrentSalary()
    {

    }
}