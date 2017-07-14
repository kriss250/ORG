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


class SideDish extends Model
{
  protected $primaryKey= "idside_dishes";
  public $timestamps = false;
  protected $table = "side_dishes";
  public $guarded = [];
  protected $connection = "mysql_pos";
}
