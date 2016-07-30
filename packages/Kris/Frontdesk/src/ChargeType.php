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

class ChargeType extends Model
{
    protected $connection = "mysql_book";
    protected $table= "charge_types";
    protected $primaryKey = "idcharge_type";
    public $timestamps = false;
}