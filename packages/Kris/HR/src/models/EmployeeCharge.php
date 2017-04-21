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

class EmployeeCharge extends Model
{
    protected $connection = "mysql_hr";
    protected $table = "employee_charges";
    public $primaryKey = "idemployee_charges";
    public $timestamps = true;
    public $guarded = [];

    public function employee()
    {
        return $this->hasOne("\Kris\HR\Models\Employee","idemployees","employee_id");
    }
}