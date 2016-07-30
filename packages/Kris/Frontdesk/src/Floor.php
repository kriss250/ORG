<?php

/**
 * Floor short summary.
 *
 * Floor description.
 *
 * @version 1.0
 * @author kris
 */

namespace Kris\Frontdesk;
use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    protected $connection = "mysql_book";
    protected $table = "floors";
    public $primaryKey = "idfloors";
    public $timestamps = false;
}