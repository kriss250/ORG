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


class OrderItem extends Model
{
 // protected $primaryKey= "order_id";
  public $timestamps = false;
  protected $table = "order_items";
  protected $connection = "mysql_pos";

  public function product()
  {
      return $this->hasOne("\App\Product","id","product_id");
  }
}
