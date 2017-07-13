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


class Order extends Model
{
  protected $primaryKey= "idorders";
  public $timestamps = false;
  protected $table = "orders";
  public $guarded = [];
  protected $connection = "mysql_pos";

  public function items()
  {
      return $this->hasMany("\App\OrderItem","order_id","idorders");
  }

  public function table()
  {
      return $this->hasOne("\App\Table","idtables","table_id");
  }

  public function waiter()
  {
      return $this->hasOne("\App\Waiter","idwaiter","waiter_id");
  }
}
