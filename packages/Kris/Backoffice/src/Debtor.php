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


class Debtor extends Model
{
    protected $connection = "mysql_backoffice";
    protected $table = "debtors";
    public $primaryKey = "iddebtors";
    protected $guarded = [];
}
