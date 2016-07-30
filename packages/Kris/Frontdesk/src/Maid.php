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

class Maid extends Model
{
    protected $connection = "mysql_book";
    protected $table = "maids";
    public $primaryKey = "idmaids";
    public $timestamps = false;

    
}