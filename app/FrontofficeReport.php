<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FrontofficeReport extends Model
{
    private static $db;

    public function __construct()
    {
        $this->connection = "mysql";
        self::$db = \DB::connection("mysql");
    }

    public function Sales($range)
    {

        return ["data"=>self::$db->select("select idreservation,room_number,type_name,is_group,concat_ws(' ',guest.firstname,guest.lastname) as guest,companies.name as Company,concat(adults,'/',children) as pax,
        checkin,checkout,payer,night_rate,due_amount,'1' as gsize from reservations
        join guest on guest.id_guest = reserved_rooms.guest_in
        join rooms on rooms.idrooms = reserved_rooms.room_id
        join room_types on room_types.idroom_types = rooms.type_id
        left join companies on companies.idcompanies = reservations.company_id
        where reservations.status not in (2,3,4) and (date(reservations.date) between ? and ?) order by idreservation desc",$range)];

    }

    public function Deposit($range)
    {
        return ["data"=>self::$db->select("SELECT * FROM cash_deposit
            join cashdeposit_dpt on iddpt = cash_deposit.dpt
            where date(date) between ? and ?",$range)];
    }

    public function ServiceSales($range)
    {
        return ["data"=>self::$db->select("SELECT type_name,room_number,concat_ws(' ',firstname,lastname) as guest,room_charges.date,motif,amount,user FROM room_charges
            join reservations on idreservation  = room_charges.reservation_id
            join rooms on rooms.idrooms = reservations.room_id
            join room_types on rooms.type_id = idroom_types
            join guest on guest.id_guest = guest_id where room_charges.reservation_id = idreservation and (date(room_charges.date) between ? and ?) ",$range)];
    }

    public function Arrival($range,$expected=false)
    {
        return ["data"=>self::$db->select("select concat_ws(' ',firstname,lastname) as guest,room_number,companies.name,type_name ,guest.country,date(checkin) as checkin,coalesce(date(checked_out), date(checkout)) as checkout,night_rate,payer from reservations
            join rooms on rooms.idrooms = reservations.room_id
            join room_types on room_types.idroom_types = rooms.type_id
            left join guest on guest.id_guest = guest_id
            left join companies on companies.idcompanies =reservations.company_id
            where  ".($expected ? "checked_in is null and reservations.status='".\Kris\Frontdesk\Reservation::ACTIVE."'" : "checked_in is not null"). " and (date(checkin) between ? and ?)",$range  )];
    }

    public function Departure($range,$expected=false)
    {
        //array_push($range,$range[1]);

        return ["data"=>self::$db->select("select idreservation,pay_by_credit,shifted,date(checked_in) as checked_in,checkin, coalesce(date(checked_out),date(checkout)) as checked_out,due_amount,rooms.room_number,concat_ws(' ',firstname,lastname) as guest,companies.name as company,date(checkin) as checkin,
            date(checkout) as checkout,night_rate,paid_amount, sum(acco_charges.amount) as acco,'1' as gsize,
            (select sum(room_charges.amount) as services from room_charges where room_charges.reservation_id = idreservation) as services ,is_group, payer from reservations

            join rooms on rooms.idrooms = reservations.room_id
            left join acco_charges on acco_charges.reservation_id = reservations.idreservation
            join guest on guest.id_guest = guest_id
            left join companies on companies.idcompanies = reservations.company_id
            where checked_in is not null and ".($expected ? " checked_out is null and date(checkout) between ? and ?  " : " reservations.status=6 and date(last_check_out)='$range[1]' and (date(checkout) between ? and ?) ")."
             group by idreservation order by idreservation asc",$range )];
    }

    public function Morning($range)
    {
        return ["data"=>self::$db->select("select idreservation,room_number,concat_ws(' ',firstname,lastname) as guest,name as company,night_rate,is_group,reservations.pay_by_credit,
            (select count(reservation_id) as size from reserved_rooms where reservation_id=idreservation) as gsize,
            (select coalesce(sum(credit),0) from folio where reservation_id = idreservation and paymethod=1 and void =0) as cash,
            (select coalesce(sum(credit),0) from folio where reservation_id = idreservation and paymethod=2 and void =0) as cc,
            (select coalesce(sum(credit),0) from folio where reservation_id = idreservation and paymethod=3 and void =0) as chec
             from rooms
            left join reserved_rooms on reserved_rooms.room_id = idrooms and checked_in is not null
            left join reservations on idreservation = reserved_rooms.reservation_id
            left join companies on companies.idcompanies = reservations.company_id
            left join guest on guest.id_guest = guest_in
            where (date(checkout) between ? and ?) and checked_out is null
            order by idreservation desc
        " ,$range)];
    }

    public function OfficeControl($range)
    {
        $date  = $range[0];

        return ["data"=>self::$db->select("
            select type_name,package_name,room_number,night_rate,
(select sum(amount) from room_charges where reservation_id=idreservation and charge=3 and date(date)='$date') as bar,
        (select sum(amount) from room_charges where reservation_id=idreservation and charge=2 and date(date)='$date') as resto,
         (select sum(amount) from room_charges where reservation_id=idreservation and charge not in(3,2) and date(date)='$date') as other,
(select sum(amount) from room_charges where reservation_id=idreservation and date(date)<='$date') as charges,
        (select sum(amount) from acco_charges where reservation_id=idreservation and date  <='$date' ) as acco,
(SELECT COALESCE(sum(credit),0)-COALESCE(sum(debit),0) FROM folio where reservation_id=idreservation and date(folio.date) <='$date') as payments,

idreservation,shifted,room_number,paid_amount,COALESCE(checked_in,checkin) as checked_in,COALESCE(checked_out,checkout) as checked_out,type_name,is_group,concat_ws(' ',guest.firstname,guest.lastname) as guest,
companies.name as Company,concat(adults,'/',children) as pax,
checkin,checkout, ifnull(nullif((select amount from acco_charges where reservation_id=idreservation and date ='$date' ),0),night_rate) as night_rate,due_amount,
'1' as gsize
from reservations
join guest on guest.id_guest = reservations.guest_id
join rooms on rooms.idrooms = reservations.room_id
left join companies on companies.idcompanies = reservations.company_id
join room_types on room_types.idroom_types = rooms.type_id
where  date(checked_in) <= '$date' and date(checkout) > '$date' and reservations.status not in (2,3,4) and checked_in is not null order by idreservation

            ")];
    }

    public function PaymentControl($range)
    {
        return ["data"=>self::$db->select("select idreservation,payer,id_account,room_number,accounts.paid_amount,type_name,is_group,concat_ws(' ',guest.firstname,guest.lastname) as guest,companies.name as Company,concat(adults,'/',children) as pax,
        checkin,coalesce(checked_out,checkout) as checkout,night_rate,due_amount,
        '1' as gsize,
        (select sum(amount) from room_charges where reservation_id=idreservation and room_id=idrooms and charge=3) as bar,
        (select sum(amount) from room_charges where reservation_id=idreservation and room_id=idrooms and charge=2) as resto,
        (select sum(amount) from room_charges where reservation_id=idreservation and room_id=idrooms and charge=4) as laundry
        from reservations
        join guest on guest.id_guest = reserved_rooms.guest_in
        join rooms on rooms.idrooms = reserved_rooms.room_id
        join room_types on room_types.idroom_types = rooms.type_id
        left join companies on companies.idcompanies = company
         where reservations.status not in (2,3,4) and date(checkout) between ? and ? order by idreservation desc",$range)];
    }

    public function Rooming($range)
    {
        return ["data"=>self::$db->select("select idreservation,shifted,room_number,id_doc,country,guest.phone,type_name,is_group,concat_ws(' ',guest.firstname,guest.lastname) as guest,companies.name as Company,concat(adults,'/',children) as pax,
        checkin,coalesce(date(checked_out), date(checkout)) as checkout,payer,night_rate,due_amount,'1' as gsize from reservations
        join guest on guest.id_guest = reservations.guest_id
        join rooms on rooms.idrooms = reservations.room_id
        join room_types on room_types.idroom_types = rooms.type_id
        left join companies on companies.idcompanies = reservations.company_id
         where checked_in is not null and reservations.status not in(2,3) and date(checkout)>? and date(checkin)<=? order by idreservation desc",$range)];

    }

    public function Breakfast($range)
    {
        $sql = "SELECT concat_ws(' ',firstname,lastname) as guest,country,room_number,type_name,concat(adults,'/',children) as pax FROM reservations
        join rooms on rooms.idrooms =reservations.room_id
        join room_types on room_types.idroom_types = rooms.type_id
        join guest on guest.id_guest = guest_id
         where reservations.status = 5
         and checked_out is null and breakfast=1 order by idreservation desc";

        return ["data"=>self::$db->select($sql)];
    }

    public function RoomTransfers($range)
    {
        $sql = "SELECT idreservation as reservation_id,from_roomnumber,to_roomnumber,concat_ws(' ',guest.firstname,guest.lastname) as guest,from_roomtype,to_roomtype,from_rate,new_rate,room_shift.date,username FROM room_shift
            join users on users.idusers = room_shift.user_id
join rooms on rooms.room_number = room_shift.to_roomnumber
join reservations on reservations.room_id=idrooms and reservations.idreservation = room_shift.reservation_id

            join guest on guest.id_guest = reservations.guest_id
            where (date(room_shift.date) between ? and ?)
        ";

        return ["data"=>self::$db->select($sql,$range)];

    }

    public function banquetOrders($range)
    {
        $sql = "select banquet_name, theme_name,concat_ws(' ',guest.firstname,guest.lastname) as guest,username, companies.name as company,total_rate,
                    banquet_event.date,arrival,departure,note,sum(banquet_payment.amount) as paid from banquet_event
                    join banquets on banquets.idbanquet = banquet_event.banquet_id
                    join banquet_themes on banquet_themes.themeid = banquet_event.theme
                    left join guest on guest.id_guest = banquet_event.guest_id
                    left join companies on companies.idcompanies = banquet_event.company_id
                    join users on users.idusers = banquet_event.user_id
                    left join banquet_payment on banquet_payment.event_id = banquet_event.idbanquet_event  where date(banquet_event.date) between ? and ?
                    group by banquet_event.idbanquet_event ";
        return ["data"=>self::$db->select($sql,$range)];
    }

    public function allBanquets()
    {
        $sql = "select * from banquets";
        return self::$db->select($sql);
    }
    public function banquetBooking($range)
    {
        $sql = "select banquet_event.idbanquet_event,date(arrival) arv,banquet_id, concat(coalesce(companies.name,''),coalesce(firstname,''),' ',coalesce(lastname,'')) as guest,pax,banquet_name,arrival,departure from banquet_event
            left join guest on guest.id_guest = guest_id
            left join companies on companies.idcompanies = company_id
            join banquets on banquets.idbanquet = banquet_id where date(arrival) between ? and ?
             order by arrival ";
        return self::$db->select($sql,$range);
    }

    public function Payments($range)
    {
        $sql = "select concat_ws(' ',guest.firstname,guest.lastname) as guest,original_amount,currencies.alias as cur,idcurrency,companies.name as company,username,credit,method_name,room_number,folio.comment, folio.date from folio
            join pay_method on pay_method.idpay_method = folio.paymethod
            join reservations on idreservation = folio.reservation_id
            join rooms on rooms.idrooms = room_id
            left join guest on guest.id_guest = reservations.guest_id
            left join currencies on currencies.idcurrency=folio.currency_id
            left join companies on idcompanies = company_id
            join users on users.idusers = folio.user_id
            where void = 0 and date(folio.date) between ? and  ?  group by folio.id_folio
        ";

        return ["data"=>self::$db->select($sql,$range)];

    }

    public static function logs($date,$cashier=0)
    {
        $cashier_str = "";

        if($cashier>0){
            array_push($date,$cashier);
            $cashier_str = " and user_id=?";
        }
    	return self::$db->select("SELECT concat(firstname,' ',lastname) as user,type,action,logs.date FROM logs join users on users.idusers=user_id where date(logs.date) between ? and ? {$cashier_str}",$date);
    }

    public static function RoomStatusChartJson()
    {
        $sql = "select concat(status_name,' (',count(*),')') as name,(count(*)/(select count(*) from rooms))*100 as y from rooms
        join room_status on rooms.status  = room_status.idroom_status
            group by status order by status_name asc";
        $data  = \DB::connection("mysql_book")->select($sql);
        return json_encode($data,JSON_NUMERIC_CHECK);
    }

    public function receptionist(Array $range,$id=0)
    {
        $range[] = $id;
        $q = "SELECT paymethod,username,sum(credit) as pay,sum(debit)as refund FROM folio join users on users.idusers=user_id where void=0 and (date(folio.date) between ? and ?) and user_id ".($id>0?"=":">")." ?  group by user_id,paymethod order by user_id";

        $pays  = \DB::connection("mysql_book")->select($q,$range);
        $sales = \DB::connection("mysql_book")->select("SELECT sum(amount) as amount,username,pay_mode,is_credit FROM misc_sales join users on users.idusers=user_id  where date(misc_sales.date) between ? and  ? and user_id ".($id>0?"=":">")." ? group by user_id,pay_mode order by user_id",$range);
        $currencies = \DB::connection("mysql_book")->select("SELECT user_id,id_folio,currencies.name,paymethod, reservation_id,sum(original_amount) FROM `folio`
        JOIN currencies on currencies.idcurrency= folio.currency_id
        WHERE void=0 and date(folio.date) between ? and  ? and folio.user_id ".($id>0?"=":">")." ? GROUP BY folio.user_id,paymethod,folio.currency_id",$range);

        return ["payments"=>$pays,"sales"=>$sales,"currencies"=>$currencies];
    }


    public function prevTurnover(Array $range)
    {
      $d1 = new Carbon($range[0]);
      $d2 = new Carbon($range[1]);
      $days = $d1->diff($d2)->days+1;
      $rangex  = $range;
      $rangex[1] = (new Carbon($rangex[0]))->addDays(-1)->format("Y-m-d");
      $rangex[0] = (new Carbon($rangex[0]))->addDays(-$days)->format("Y-m-d");

      $roomsto = self::$db->select("select sum(amount) as amount from acco_charges where date between ? and ?",$range);

      $chargesto = self::$db->select("select sum(amount) as amount from room_charges where pos = 0 and( date between ? and ?)",$rangex);
      $total = (count($roomsto) > 0 ? $roomsto[0]->amount : 0) + (count($chargesto) > 0 ? $chargesto[0]->amount : 0);
      return $total;
    }

    public function turnover(Array $range)
    {
      $roomsto = null;

      if($range[0]== \ORG\Dates::$RESTODATE)
      {
          $roomsto = self::$db->select("select sum(night_rate) as amount from reservations where status=".\Kris\Frontdesk\Reservation::CHECKEDIN." and date(checkout)<>?",[$range[0]]);
      }else {
          $roomsto = self::$db->select("select sum(amount) as amount from acco_charges where date between ? and ?",$range);
      }
      $chargesto = self::$db->select("select sum(amount) as amount from room_charges where pos = 0 and( date between ? and ?)",$range);
      $total = (count($roomsto) > 0 ? $roomsto[0]->amount : 0) + (count($chargesto) > 0 ? $chargesto[0]->amount : 0);
      return $total;
    }

    public function avg_rate(Array $range)
    {
      $avg = self::$db->select("select avg(amount) as amount from acco_charges where date between ? and ?",$range);
      return count($avg) > 0 ? $avg[0]->amount : 0;
    }

    public function prev_avg_rate(Array $range)
    {
      $d1 = new Carbon($range[0]);
      $d2 = new Carbon($range[1]);
      $days = $d1->diff($d2)->days+1;
      $rangex  = $range;
      $rangex[1] = (new Carbon($rangex[0]))->addDays(-1)->format("Y-m-d");
      $rangex[0] = (new Carbon($rangex[0]))->addDays(-$days)->format("Y-m-d");

      $avg = self::$db->select("select avg(amount) as amount from acco_charges where date between ? and ?",$rangex);
      return count($avg) > 0 ? $avg[0]->amount : 0;
    }

}
