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

class Department extends Model
{
    protected $connection = "mysql_hr";
    protected $table = "departments";
    public $primaryKey = "iddepartments";
    public $guarded = [];

    public function post()
    {
        return $this->hasMany("\Kris\HR\Models\Post","department_id","iddepartments");
    }
}