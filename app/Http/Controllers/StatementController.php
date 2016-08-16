<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class StatementController extends Controller
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
     * Summary of ShowStatement
     * @param mixed $where
     * @param mixed $id
     * @param mixed $company
     * @param mixed $individual
     */
    public function ShowStatement($where,$id,$company,$individual)
    {
        $startDate = \ORG\Dates::$RESTODATE;
        $endDate= \ORG\Dates::$RESTODATE;

        if(isset($_GET['startdate']))
        {
            $startDate = $_GET['startdate'];
        }

        if(isset($_GET['enddate']))
        {
            $endDate = $_GET['enddate'];
        }
        switch(strtolower($where))
        {
            case "pos":
                //Search POS
                $posStatement = null;
                $sql = "select * from bills where customer like ? and deleted=0 and date(date) between ? and  ?";
                $posStatement =\DB::select($sql,[$company,$startDate,$endDate]);
                return \View::make("Backoffice.POSStatement",["data"=>$posStatement,"customer"=>$company]);
            case "fo":
                //Search Front office
                $sql = "select idreservation,night_rate,rooms.room_number,
(select coalesce(sum((folio.credit)),0) from folio where reservation_id=idreservation and date(folio.date) between ? and ? ) as paid,
(select coalesce(sum(room_charges.amount),0) from room_charges where reservation_id=idreservation and date(room_charges.date) between ? and ?) as services,
( select coalesce(sum(acco_charges.amount),0) from acco_charges where reservation_id=idreservation and date(acco_charges.date) between ? and ? ) as acco,
concat_ws(' ',firstname,lastname) as guest,checkin,checkout from reservations
join guest on guest.id_guest = guest_id
join rooms on rooms.idrooms =room_id
where ".($company=='null' ? 'id_guest=?' : "company_id=?")." and date(checkin) between ? and ?
group by idreservation";
                $customer = ($company == "null" ? $individual : $company);

                $Statement =\DB::connection("mysql_book")->select($sql,[$startDate,$endDate,$startDate,$endDate,$startDate,$endDate,$id,$startDate,$endDate]);
                return \View::make("Backoffice.FOStatement",["data"=>$Statement,"customer"=>$customer]);
        }
    }

}
