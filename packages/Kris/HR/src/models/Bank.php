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

class Bank extends Model
{
    protected $connection = "mysql_hr";
    protected $table = "banks";
    public $primaryKey = "idbanks";
    public $guarded = [];
}