<?php

/**
 * Invoice short summary.
 *
 * Invoice description.
 *
 * @version 1.0
 * @author kris
 */
namespace App;

use Illuminate\Database\Eloquent\Model;


class Store extends Model
{
    protected $primaryKey= "idstore";
    public $timestamps = false;
    protected $table = "store";
    protected $connection = "mysql_pos";
}
