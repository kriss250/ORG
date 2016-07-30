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

class RoomType extends Model
{
    protected $connection = "mysql_book";
    protected $table = "room_types";
    public $primaryKey = "idroom_types";
    public $timestamps = false;

    
}