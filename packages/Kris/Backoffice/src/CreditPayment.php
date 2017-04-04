<?php

/**
 * Creditor short summary.
 *
 *
 * @version 1.0
 * @author kris
 */
namespace App;

use Illuminate\Database\Eloquent\Model;


class CreditPayment extends Model
{
    protected $connection = "mysql_backoffice";
    protected $table = "credit_payment";
    protected $primaryKey = "idpayment";
    protected $guarded = [];
}
