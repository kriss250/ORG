<?php

/**
 * RoomType short summary.
 *
 * RoomType description.
 *
 * @version 1.0
 * @author kris
 */

namespace Kris\Frontdesk;
use Illuminate\Database\Eloquent\Model;

class RateType extends Model
{
    protected $connection = "mysql_book";
    protected $table = "rate_types";
    public $primaryKey = "idrate_types";
    public $timestamps = false;
}