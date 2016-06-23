<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $sql = "select concat_ws(' ',guest.firstname,guest.lastname) as guest,balance_amount,due_amount,coalesce(sum(acco_charges.amount),0) as acco,username,guest.phone,night_rate,companies.name as company,country,rooms.room_number,checkin,checkout from reserved_rooms
join reservations on reservations.idreservation = reservation_id
join guest on guest.id_guest = guest_in
join rooms on rooms.idrooms = room_id
left join users on users.idusers = checkedin_by
join accounts on accounts.reservation_id = idreservation
left join acco_charges on acco_charges.reservation_id =idreservation
left join companies on  companies.idcompanies = company_id
where reserved_rooms.reservation_id =?";

        $resInfo = \DB::connection("mysql_book")->select($sql,[$id]);
        $charges = \DB::connection("mysql_book")->select("select motif,amount,user,date from room_charges where reservation_id=?",[$id]);
        $pays = \DB::connection("mysql_book")->select("select credit,motif,method_name,username,folio.date from folio
            join pay_method on pay_method.idpay_method = folio.paymethod
            join users on users.idusers = folio.user_id
             where void = 0 and reservation_id=?",[$id]);

        $resInfo = isset($resInfo[0]) ? $resInfo[0] : $resInfo;

        return \View::make("Backoffice.ViewReservation",["info"=>$resInfo,"charges"=>$charges,"payments"=>$pays]);
    }

}
