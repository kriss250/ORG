<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayMode extends Model
{
  protected $connection = "mysql_book";
  protected $table = "pay_method";
  public $primaryKey = "idpay_method";
  protected $guarded = [];
}
