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

class PayrollEmployee extends Model
{
    protected $connection = "mysql_hr";
    protected $table = "payroll_employee";
    public $primaryKey = "payroll_id";
    public $guarded = [];
    public $timestamps = false;

    public function employee()
    {
        return $this->hasOne("\Kris\HR\Models\Employee","idemployees","employee_id");
    }
}