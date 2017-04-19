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

class Payroll extends Model
{
    protected $connection = "mysql_hr";
    protected $table = "payroll";
    public $primaryKey = "idpayroll";
    public $guarded = [];

    public function charge()
    {
        return $this->hasManyThrough("\Kris\HR\Models\Charge","\Kris\HR\Models\PayrollCharge","charge_id","charge_id");
    }

    public function tax()
    {

    }
}