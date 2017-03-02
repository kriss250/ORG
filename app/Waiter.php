<?php

/**
 * Bill short summary.
 *
 * Invoice description.
 *
 * @version 1.0
 * @author kris
 */
namespace App;

use Illuminate\Database\Eloquent\Model;


class Waiter extends Model
{
  protected $primaryKey= "idwaiter";
  public $timestamps = false;
  protected $table = "waiters";
  protected $connection = "mysql_pos";
}
