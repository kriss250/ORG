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

class PayrollCharge extends Model
{
    protected $connection = "mysql_hr";
    protected $table = "payroll_charges";
    public $primaryKey = "payroll_id";
    public $guarded = [];
    public $timestamps = false;

    public function charge()
    {
        return $this->hasOne("\Kris\HR\Models\Charge","idcharges","charge_id");
    }
}