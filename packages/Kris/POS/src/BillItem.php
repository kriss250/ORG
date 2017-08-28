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


class BillItem extends Model
{
  protected $primaryKey= "product_id";
  public $timestamps = false;
  protected $table = "bill_items";
  protected $connection = "mysql_pos";
  public $guarded = [];
}
