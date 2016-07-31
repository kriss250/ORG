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

class Room extends Model
{
    protected $connection = "mysql_book";
    protected $table= "rooms";
    protected $primaryKey = "idrooms";
    public $timestamps = false;
    protected $guarded = [];
    public function type()
    {
        return $this->belongsTo("Kris\Frontdesk\RoomType","type_id","idroom_types");
    }

    public function floor()
    {
        return $this->belongsTo("Kris\Frontdesk\Floor","floors_id","idfloors");
    }

    public function rstatus()
    {
        return $this->hasOne("Kris\Frontdesk\RoomStatus","status_code","status");
    }



    public static function vacant()
    {
        return self::where("status",RoomStatus::VACANT)->join("room_types","idroom_types","=","type_id")->join("room_rates","room_type_id","=","type_id")->join("floors","floors_id","=","idfloors")->get();
    }

    public function availableRooms($checkin,$checkout)
    {
        $sql = "select room_id,date(checkin) as checkin,date(checkout) as checkout from reservations where date(checkin) >=? and date(checkin)<? and reservations.status not in(".(\Kris\Frontdesk\Reservation::CANCELLED).",".(\Kris\Frontdesk\Reservation::NOSHOW).")";

        $reserved_rooms = \DB::connection($this->connection)->select($sql,[\Kris\Frontdesk\Env::WD()->format("Y-m-d"),$checkout]);

        $not_av_rooms = [];

        foreach($reserved_rooms as $room)
        {
            if($room->checkin == $checkin)
            {
                $not_av_rooms[] = $room->room_id;
                continue;
            }

            if($checkin < $room->checkin && $checkout > $room->checkout)
            {
                $not_av_rooms[] = $room->room_id;
                continue;
            }

            //Falls in the interval
            if(($checkin >= $room->checkin && $checkin < $room->checkout) || ($checkout >= $room->checkin && $checkout <= $room->checkout)  )
            {
                $not_av_rooms[] = $room->room_id;
            }


        }

        return $this->whereNotIn("idrooms",$not_av_rooms)->whereNotIn("status",[RoomStatus::BLOCKED,RoomStatus::HOUSEUSE])->join("room_types","idroom_types","=","type_id")->join("room_rates","room_type_id","=","type_id")->join("floors","floors_id","=","idfloors")->get();

    }

    /**
     * Summary of isAvailable
     * @param mixed $roomid
     * @param mixed $checkin
     * @param mixed $checkout
     * @param mixed $excluded Excluded reservationid
     * @return bool
     */
    public function isAvailable($roomid,$checkin,$checkout,$excluded=0)
    {
        $sql = "select room_id,date(checkin) as checkin,date(checkout) as checkout from reservations where date(checkin) >=? and date(checkin)<? and room_id=? and idreservation<>? and reservations.status not in(".(\Kris\Frontdesk\Reservation::CANCELLED).",".(\Kris\Frontdesk\Reservation::NOSHOW).")";

        $reserved_rooms = \DB::connection($this->connection)->select($sql,[\Kris\Frontdesk\Env::WD()->format("Y-m-d"),$checkout,$roomid,$excluded]);

        foreach($reserved_rooms as $room)
        {
            #!!!
            if($room->checkin == $checkin && $room->checkout > $checkin)
            {
                return false;
            }

            //Covers another reservation
            if($checkin < $room->checkin && $checkout > $room->checkout)
            {
                return false;
            }

            //Falls in the interval
            if(($checkin >= $room->checkin && $checkin < $room->checkout) || ($checkout >= $room->checkin && $checkout <= $room->checkout)  )
            {
                return false;
            }


        }

        return true;

    }
}