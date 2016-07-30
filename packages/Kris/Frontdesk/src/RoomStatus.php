<?php

/**
 * Company short summary.
 *
 * Company description.
 *
 * @version 1.0
 * @author kris
 */

namespace Kris\Frontdesk;
use Illuminate\Database\Eloquent\Model;

class RoomStatus extends Model
{
    public $timestamps = false;
    protected $table = "room_status";
    protected $primaryKey = "idroom_status";
    protected $connection = "mysql_book";

    const VACANT = 1;
    const OCCUPIED  = 2;
    const BLOCKED = 3;
    const RESERVED =4;
    const DIRTY  =5;
    const HOUSEUSE = 6;
    const CHECKEDOUT = 7;

    const VACANTCOLOR = "green";


    public function getStatusCount()
    {
        return $this->select(\DB::raw("count(status) as cnt,status_code"))->leftJoin("rooms","status_code","=","rooms.status")->groupBy("status_code")->get();
    }
}