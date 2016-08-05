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
        $acc_sql = "SELECT amount as unit_price,count(date) as qty,date,concat('Accomodation: ' ,room_number) as motif,'charge' as type FROM  acco_charges
                where reservation_id =" . $reservationid ."
                group by room_id,amount
                ";

        $charge_types ="1,2,3,4,5";

        if(\Request::has("charge_filter"))
        {
            $filter_data = \Request::all();
            array_pop($filter_data);
            $data_filter = array_keys($filter_data);
            $charge_types = "";
            foreach($data_filter as  $param)
            {
                if(explode("_",$param)[0]=="charge")
                {
                    $charge_types .= $filter_data[$param].",";
                }else {
                    continue;
                }
            }

            $charge_types = substr($charge_types,0,strlen($charge_types)-1);
        }

        $services_sql = "
                select room_charges.amount as unit_price,1 as qty,date(room_charges.date) as date,motif,'charge' as type from room_charges
                join charge_types on idcharge_type = room_charges.charge
                join users on idusers = room_charges.user_id
                where reservation_id = " . $reservationid ." ".(strlen($charge_types)? "and room_charges.charge in($charge_types)" : "");

        $q = $acc_sql." union all ".$services_sql;

        $pays = "SELECT (coalesce(debit,0)+coalesce(credit,0)) as unit_price,'1' as qty,date(date) as date,concat('Payment: ' ,motif) as motif,'payment' as type FROM folio
                where reservation_id =" . $reservationid ."
                ";

        switch($_GET['type'])
        {
            case "standard":
                $q = $acc_sql." union all ".$services_sql;
                break;
            case "services":
                $q =$services_sql;
                break;
            case "accomodation":
                $q = $acc_sql;
                break;
            case "payments":
                $q = $acc_sql." union all ".$services_sql." union all ".$pays;
                break;
        }

        return \DB::connection("mysql_book")->select(\DB::raw($q));

    }
}