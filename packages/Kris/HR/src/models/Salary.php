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

class Salary extends Model
{
    protected $connection = "mysql_hr";
    protected $table = "employee_salary";
    public $primaryKey = "idemployee_salary";
    public $guarded = [];

    public static function avgSalaryPerDepartmentChart()
    {
        return self::select(\DB::raw("distinct name, avg(amount) as y"))
            ->join("employees","idemployees","=","employee_id")
            ->join("departments","department_id","=","iddepartments")
            ->orderBy("amount","desc")
            ->groupBy("department_id")->get()->toJson();
    }
}