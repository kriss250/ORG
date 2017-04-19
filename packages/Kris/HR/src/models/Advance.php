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

class Advance extends Model
{
    protected $connection = "mysql_hr";
    protected $table = "advances";
    public $primaryKey = "idadvances";
    public $timestamps = false;
    public $guarded = [];

    public function employee()
    {
        return $this->hasOne("\Kris\HR\Models\Employee","idemployees","employee_id");
    }
}