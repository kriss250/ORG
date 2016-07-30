<?php

/**
 * Charge short summary.
 *
 * Charge description.
 *
 * @version 1.0
 * @author kris
 */

namespace Kris\Frontdesk;
use Illuminate\Database\Eloquent\Model;

class PayMethod extends Model
{
    protected $connection = "mysql_book";
    protected $table= "pay_method";
    protected $primaryKey = "idpay_method";
    public $timestamps = false;
}