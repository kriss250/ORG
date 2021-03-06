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

class EmployeeLeave extends Model
{
    protected $connection = "mysql_hr";
    protected $table = "employee_leaves";
    public $primaryKey = "idemployee_leaves";
    public $guarded = [];

    public function employee()
    {
        return $this->hasOne("\Kris\HR\Models\Employee","idemployees","employee_id");
    }
}