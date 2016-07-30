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

class Bill extends Model
{
    protected $connection ="mysql_book";

    public static function getBillItems($reservationid)
    {
        $acc_sql = "SELECT amount as unit_price,count(date) as qty,date,concat('Accomodation: ' ,room_number) as motif FROM  acco_charges
                where reservation_id =" . $reservationid ."
                group by room_id,amount
                ";


        $services_sql = "
                select sum(room_charges.amount) as unit_price,1 as qty,date(room_charges.date),motif from room_charges
                join charge_types on idcharge_type = room_charges.charge
                join users on idusers = room_charges.user_id
                where reservation_id = " . $reservationid ." group by idroom_charge";

        return \DB::connection("mysql_book")->select(\DB::raw($acc_sql." union all ".$services_sql));

    }
}