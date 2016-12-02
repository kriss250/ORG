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


class Product extends Model
{
  protected $primaryKey= "id";
  public $timestamps = false;
  protected $table = "products";
  protected $connection = "mysql_pos";

  public function price()
  {
      return $this->hasOne("\App\ProductPrice","id","price_id");
  }
}
