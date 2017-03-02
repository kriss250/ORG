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


class Table extends Model
{
  protected $primaryKey= "idtables";
  public $timestamps = false;
  protected $table = "tables";
  protected $connection = "mysql_pos";
}
