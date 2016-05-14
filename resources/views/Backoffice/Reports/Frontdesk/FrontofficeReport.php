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
        return ["data"=>self::$db->select("select room_number,type_name,is_group,concat_ws(' ',guest.firstname,guest.lastname) as guest,companies.name as Company,concat(adults,'/',children) as pax,
        checkin,checkout,night_rate,due_amount from reservations
        join  reserved_rooms on reserved_rooms.reservation_id =reservations.idreservation
        join guest on guest.id_guest = reserved_rooms.guest_in
        join rooms on rooms.idrooms = reserved_rooms.room_id
        join room_types on room_types.idroom_types = rooms.type_id
        left join companies on companies.idcompanies = company
        join accounts on accounts.reservation_id = idreservation where reservations.status not in (2,3,4)")];
    }

    public function OfficeControl($range)
    {
        return ["data"=>self::$db->select("select room_number,type_name,is_group,concat_ws(' ',guest.firstname,guest.lastname) as guest,companies.name as Company,concat(adults,'/',children) as pax,
        checkin,checkout,night_rate,due_amount from reservations
        join  reserved_rooms on reserved_rooms.reservation_id =reservations.idreservation
        join guest on guest.id_guest = reserved_rooms.guest_in
        join rooms on rooms.idrooms = reserved_rooms.room_id
        join room_types on room_types.idroom_types = rooms.type_id
        left join companies on companies.idcompanies = company
        join accounts on accounts.reservation_id = idreservation where reservations.status not in (2,3,4)")];
    }
}
