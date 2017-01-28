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

class RoomRate extends Model
{
    protected $connection = "mysql_book";
    public static $_connection = "mysql_book";
    protected $table= "room_rates";
    public $guarded = [];
    public $primaryKey = "rate_type_id";
    public $timestamps = false;
}