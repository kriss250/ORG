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


class CashbookTransaction extends Model
{
    protected $connection = "mysql_backoffice";
    protected $table = "cashbook_transactions";
    public $primaryKey = "transactionid";
    protected $guarded = [];

    public function cashbook()
    {
      return $this->hasOne("\App\Cashbook","cashbookid","cashbook_id");
    }
}
