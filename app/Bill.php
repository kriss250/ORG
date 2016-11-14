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


class Bill extends Model
{
  protected $primaryKey= "idbills";
  public $timestamps = false;
  protected $table = "bills";
  protected $connection = "mysql_pos";
}
