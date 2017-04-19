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

class BankAccount extends Model
{
    protected $connection = "mysql_hr";
    protected $table = "bank_accounts";
    public $primaryKey = "idbank_accounts";
    public $timestamps = false;
    public $guarded = [];

    public function employee()
    {
        return $this->hasOne("\Kris\HR\Models\Employee","idemployees","employee_id");
    }
}