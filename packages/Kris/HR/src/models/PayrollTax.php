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

class PayrollTax extends Model
{
    protected $connection = "mysql_hr";
    protected $table = "payroll_taxes";
    public $primaryKey = "payroll_id";
    public $guarded = [];
    public $timestamps = false;

    public function tax()
    {
        return $this->hasOne("\Kris\HR\Models\Tax","idtaxes","tax_id");
    }
}