<?php

/**
 * Room short summary.
 *
 * Room description.
 *
 * @version 1.0
 * @author kris
 */
namespace Kris\Frontdesk;
use Illuminate\Database\Eloquent\Model;

class Housekeeping extends Model
{
    protected $connection = "mysql_book";
    protected $table = "housekeeping";
    public $primaryKey = null;
    public $timestamps = false;

    public function maid()
    {
        return $this->hasOne("\Kris\Frontdesk\Maid","idmaids","maid_id");
    }

    public function room()
    {
        return $this->hasOne("\Kris\Frontdesk\Room","idrooms","room_id");
    }
}