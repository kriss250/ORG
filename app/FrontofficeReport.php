<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
        checkin,checkout,payer,night_rate,due_amount,(select count(reservation_id) as size from reserved_rooms where reservation_id=idreservation) as gsize from reservations
        join  reserved_rooms on reserved_rooms.reservation_id =reservations.idreservation
        join guest on guest.id_guest = reserved_rooms.guest_in
        join rooms on rooms.idrooms = reserved_rooms.room_id
        join room_types on room_types.idroom_types = rooms.type_id
        left join companies on companies.idcompanies = reservations.company_id
        join accounts on accounts.reservation_id = idreservation where reservations.status not in (2,3,4) and (date(reservations.date) between ? and ?) order by idreservation desc",$range)];

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
            join rooms on rooms.idrooms = room_id
            join reserved_rooms on reserved_rooms.room_id = idrooms and reserved_rooms.reservation_id = room_charges.reservation_id
            join room_types on rooms.type_id = idroom_types
            join guest on guest.id_guest = guest_in where (date(room_charges.date) between ? and ?) ",$range)];
    }

    public function Arrival($range,$expected=false)
    {
        return ["data"=>self::$db->select("select concat_ws(' ',firstname,lastname) as guest,room_number,companies.name,type_name ,guest.country,date(checkin) as checkin,coalesce(date(checked_out), date(checkout)) as checkout,night_rate,payer from reserved_rooms
            join reservations on idreservation = reserved_rooms.reservation_id
            join rooms on rooms.idrooms = reserved_rooms.room_id
            join room_types on room_types.idroom_types = rooms.type_id
            join guest on guest.id_guest = guest_in
            left join companies on companies.idcompanies =reservations.company_id
            where  ".($expected ? "checked_in is null" : "checked_in is not null"). " and (date(checkin) between ? and ?) and shifted=0",$range  )];
    }

    public function Departure($range,$expected=false)
    {
        //array_push($range,$range[1]);

        return ["data"=>self::$db->select("select idreservation,pay_by_credit,shifted,date(checked_in) as checked_in,checkin, coalesce(date(checked_out),date(checkout)) as checked_out,due_amount,rooms.room_number,concat_ws(' ',firstname,lastname) as guest,companies.name as company,date(checkin) as checkin,
            date(checkout) as checkout,night_rate,balance_amount, sum(acco_charges.amount) as acco,
(select count(reservation_id) as size from reserved_rooms where reservation_id=idreservation) as gsize,
            (select sum(room_charges.amount) as services from room_charges where room_id=idrooms and room_charges.reservation_id = idreservation and  reserved_room_id = reserved_rooms.idreserved_rooms) as services ,is_group, payer from reserved_rooms
            join accounts on accounts.reservation_id= reserved_rooms.reservation_id
            join rooms on rooms.idrooms = reserved_rooms.room_id
            join reservations on reservations.idreservation = reserved_rooms.reservation_id ".(!$expected ? " and reservations.status=6 and date(last_check_out)='$range[1]'" : " ")."
            left join acco_charges on acco_charges.reservation_id = reserved_rooms.reservation_id and acco_charges.room_id = reserved_rooms.room_id and acco_charges.reserved_room_id = reserved_rooms.idreserved_rooms
            join guest on guest.id_guest = guest_in
            left join companies on companies.idcompanies = reservations.company_id
            where checked_in is not null and ".($expected ? " checked_out is null " : " (date(checkout) between ? and ?) or (shifted=1 and reserved_rooms.reservation_id=idreservation)")."
             group by idreserved_rooms,idreservation order by idreservation,idreserved_rooms asc",$range )];
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
        $rangex = $range;
        array_push($range,$range[0]);
        array_push($range,$range[0]);
        $d  = isset($range[0]) ? $range[0] : \ORG\Dates::$RESTODATE;

        return ["data"=>self::$db->select("select idreservation,shifted,room_number,accounts.balance_amount,COALESCE(checked_in,checkin) as checked_in,COALESCE(checked_out,checkout) as checked_out,type_name,is_group,concat_ws(' ',guest.firstname,guest.lastname) as guest,
companies.name as Company,concat(adults,'/',children) as pax,
        checkin,checkout,night_rate,due_amount,(select count(reservation_id) as size from reserved_rooms where reservation_id=idreservation) as gsize,
        (select sum(amount) from room_charges where reservation_id=idreservation and room_id=idrooms and charge=3 and date(date)='$d') as bar,
        (select sum(amount) from room_charges where reservation_id=idreservation and room_id=idrooms and charge=2 and date(date)='$d') as resto,
        (select sum(amount) from room_charges where reservation_id=idreservation and room_id=idrooms and charge=4 and date(date)='$d') as laundry
        from reservations
        join  reserved_rooms on reserved_rooms.reservation_id = reservations.idreservation
        join guest on guest.id_guest = reserved_rooms.guest_in
        join rooms on rooms.idrooms = reserved_rooms.room_id
        join room_types on room_types.idroom_types = rooms.type_id
        left join companies on companies.idcompanies = reservations.company_id
        join accounts on accounts.reservation_id = idreservation where reservations.status not in (2,3,4) and date(checkout) >= ? or ( date(checkout) >= ? and date(checkout) <> ? ) or (shifted=1 and date(checkout) >=?)  and checked_in is not null order by idreservation desc",$range)];
    }

    public function PaymentControl($range)
    {
        return ["data"=>self::$db->select("select idreservation,payer,id_account,room_number,accounts.balance_amount,type_name,is_group,concat_ws(' ',guest.firstname,guest.lastname) as guest,companies.name as Company,concat(adults,'/',children) as pax,
        checkin,coalesce(checked_out,checkout) as checkout,night_rate,due_amount,
        (select count(reservation_id) as size from reserved_rooms where reservation_id=idreservation) as gsize,
         (select sum(amount) from room_charges where reservation_id=idreservation and room_id=idrooms and charge=3) as bar,
        (select sum(amount) from room_charges where reservation_id=idreservation and room_id=idrooms and charge=2) as resto,
        (select sum(amount) from room_charges where reservation_id=idreservation and room_id=idrooms and charge=4) as laundry
        from reservations
        join  reserved_rooms on reserved_rooms.reservation_id =reservations.idreservation
        join guest on guest.id_guest = reserved_rooms.guest_in
        join rooms on rooms.idrooms = reserved_rooms.room_id
        join room_types on room_types.idroom_types = rooms.type_id
        left join companies on companies.idcompanies = company
        join accounts on accounts.reservation_id = idreservation where reservations.status not in (2,3,4) and date(checkout) between ? and ? order by idreservation desc",$range)];
    }

    public function Rooming($range)
    {
        return ["data"=>self::$db->select("select idreservation,shifted,room_number,id_doc,country,guest.phone,type_name,is_group,concat_ws(' ',guest.firstname,guest.lastname) as guest,companies.name as Company,concat(adults,'/',children) as pax,
        checkin,coalesce(date(checked_out), date(checkout)) as checkout,payer,night_rate,due_amount,(select count(reservation_id) as size from reserved_rooms where reservation_id=idreservation and checked_out is null) as gsize from reservations
        join  reserved_rooms on reserved_rooms.reservation_id =reservations.idreservation
        join guest on guest.id_guest = reserved_rooms.guest_in
        join rooms on rooms.idrooms = reserved_rooms.room_id
        join room_types on room_types.idroom_types = rooms.type_id
        left join companies on companies.idcompanies = reservations.company_id
        join accounts on accounts.reservation_id = idreservation where checked_out is null and reservations.status=5 order by idreservation desc")];

    }

    public function Breakfast($range)
    {
        $sql = "SELECT concat_ws(' ',firstname,lastname) as guest,country,room_number,type_name,concat(adults,'/',children) as pax FROM reservations
        join reserved_rooms on reserved_rooms.reservation_id = idreservation
        join rooms on rooms.idrooms =reserved_rooms.room_id
        join room_types on room_types.idroom_types = rooms.type_id
        join guest on guest.id_guest = guest_in
         where reservations.status = 5
         and checked_out is null and breakfast=1 order by idreservation desc";

        return ["data"=>self::$db->select($sql)];
    }

    public function RoomTransfers($range)
    {
        $sql = "SELECT from_roomnumber,to_roomnumber,concat_ws(' ',guest.firstname,guest.lastname) as guest,from_roomtype,to_roomtype,from_rate,new_rate,room_shift.date,username FROM room_shift
            join users on users.idusers = room_shift.user_id
            join rooms on rooms.room_number = room_shift.to_roomnumber
            join reserved_rooms on reserved_rooms.room_id=idrooms and reserved_rooms.reservation_id = room_shift.reservation_id
            join guest on guest.id_guest = reserved_rooms.guest_in
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
        $sql = "select concat_ws(' ',guest.firstname,guest.lastname) as guest,companies.name as company,username,credit,method_name,room_number,folio.comment, folio.date from folio
            join pay_method on pay_method.idpay_method = folio.paymethod
            join reserved_rooms  on reserved_rooms.reservation_id = folio.reservation_id
            join reservations on idreservation = reserved_rooms.reservation_id
            join rooms on rooms.idrooms = room_id
            join guest on guest.id_guest = guest_in
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
}
