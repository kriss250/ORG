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


class Creditor extends Model
{
    protected $connection = "mysql_backoffice";
    protected $table = "creditors";
    public $primaryKey = "id";
    protected $guarded = [];
}
