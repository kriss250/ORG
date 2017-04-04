<?php

/**
 * Room short summary.
 *
 * Room description.
 *
 * @version 1.0
 * @author kris
 */
namespace Kris\HR;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $connection = "mysql_hr";
    protected $table = "employees";
    public $primaryKey = "idemployees";
    public $timestamps = false;
}