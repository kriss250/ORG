<?php

/**
 * Invoice short summary.
 *
 * Invoice description.
 *
 * @version 1.0
 * @author kris
 */
namespace App;

use Illuminate\Database\Eloquent\Model;


class Credit extends Model
{
    protected $connection = "mysql_backoffice";
    protected $table = "credit";
    public $primaryKey = "id";
    protected $guarded = [];

    public function creditor()
    {
      return $this->hasOne("\App\Creditor","idcreditors","creditor_id");
    }

    public function payment()
    {
      return $this->hasMany("\App\CreditPayment","credit_id","id");
    }
}
