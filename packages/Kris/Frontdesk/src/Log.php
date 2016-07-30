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

class Log extends Model
{
    protected $connection = "mysql_book";
    protected $table = "logs";
    public $primaryKey = "idlogs";
    public $timestamps = false;
    protected $guarded  = [];
}