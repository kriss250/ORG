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

class Address extends Model
{
    protected $connection = "mysql_hr";
    protected $table = "address";
    public $guarded = [];
    public $primaryKey = "idaddress";
    public $timestamps = false;
}