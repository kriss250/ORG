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

class Contact extends Model
{
    protected $connection = "mysql_hr";
    protected $table = "contacts";
    public $guarded = [];
    public $timestamps = false;
}