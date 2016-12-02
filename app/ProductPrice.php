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


class ProductPrice extends Model
{
  protected $primaryKey= "id";
  public $timestamps = false;
  protected $table = "product_price";
  protected $connection = "mysql_pos";
}
