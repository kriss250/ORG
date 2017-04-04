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

class Post extends Model
{
    protected $connection = "mysql_hr";
    protected $table = "posts";
    public $primaryKey = "idposts";
    public $guarded = [];

    public function department()
    {
        return $this->hasOne("\Kris\HR\Models\Department","iddepartments","department_id");
    }
}