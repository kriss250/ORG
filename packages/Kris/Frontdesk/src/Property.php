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

class Property extends Model
{
    protected $connection = "mysql_book";
    protected $table = "hotel";
    public $primaryKey = "idhotel";
    public $timestamps = false;
}