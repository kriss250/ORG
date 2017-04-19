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

class EmployeeContract extends Model
{
    protected $connection = "mysql_hr";
    protected $table = "employee_contract";
    public $primaryKey = "idcontracts";
    public $timestamps = true;
    public $guarded = [];

}