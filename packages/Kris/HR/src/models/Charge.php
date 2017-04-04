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

class Charge extends Model
{
    protected $connection = "mysql_hr";
    protected $table = "charges";
    public $primaryKey = "idcharges";
    public $guarded = [];
}