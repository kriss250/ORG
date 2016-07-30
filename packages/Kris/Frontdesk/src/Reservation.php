<?php

/**
 * Reservation short summary.
 *
 * Reservation description.
 *
 * @version 1.0
 * @author kris
 */
namespace Kris\Frontdesk;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
         const  ACTIVE = 1;
         const  CANCELLED = 2;
         const  VOID = 3;
         const  NOSHOW = 4;
         const  CHECKEDIN = 5;
         const  CHECKEDOUT = 6;

    protected $connection = "mysql_book";
    protected $table = "reservations";
    public $primaryKey = "idreservation";
    protected $guarded = [];
    public $timestamps = false;
    public function guest()
    {
        return $this->belongsTo("Kris\Frontdesk\Guest","guest_id","id_guest");
    }

    public function company()
    {
        //return $this->leftJoin("companies","companies.idcompanies","=","company_id")->get()->first();
        return $this->hasOne("\Kris\Frontdesk\Company","idcompanies","company_id");
    }

    public function cancel()
    {
        $this->update([
            "status"=>self::CANCELLED,
            "checked_out"=>\Kris\Frontdesk\Env::WD()->format("Y-m-d")
            ]);
        if($this->room->status == \Kris\Frontdesk\RoomStatus::RESERVED){
            $this->room->update([
                "status"=>\Kris\Frontdesk\RoomStatus::VACANT
                ]);
        }

        \FO::log("Cancelled Reservation #".$this->idreservation);
    }

    public function noShow()
    {
        $this->update([
           "status"=>self::NOSHOW,
           "checked_out"=>\Kris\Frontdesk\Env::WD()->format("Y-m-d")
           ]);

        if($this->room->status == \Kris\Frontdesk\RoomStatus::RESERVED){
            $this->room->update([
                "status"=>\Kris\Frontdesk\RoomStatus::VACANT
                ]);
        }

        \FO::log("Marked Reservation #".$this->idreservation." as No show");

    }


    public function room()
    {
        return $this->hasOne("Kris\Frontdesk\Room","idrooms","room_id");
    }

    public function charge()
    {
        return $this->hasMany("\Kris\Frontdesk\Charge","reservation_id","idreservation");
    }

    public function payments()
    {
        return $this->hasMany("\Kris\Frontdesk\Payment","reservation_id","idreservation");
    }


}