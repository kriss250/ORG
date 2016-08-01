<?php

/**
 * OperationsController short summary.
 *
 * OperationsController description.
 *
 * @version 1.0
 * @author kris
 */
namespace Kris\Frontdesk\Controllers;
use App\Http\Controllers\Controller;
use \DB;

class StatementsController extends Controller
{
    public function guest($id)
    {
        $guest = \Kris\Frontdesk\Guest::find($id);
        $res = null;
        if(isset($_GET['startdate']) && isset($_GET['enddate'])){
            $res = \Kris\Frontdesk\Reservation::whereRaw("(date(checkin) between ? and ? or date(checkout) between ? and ?)")->where("guest_id",$id)->setBindings([$_GET['startdate'],$_GET['enddate'],$_GET['startdate'],$_GET['enddate'],$id])->get();
        }

        return \View::make("Frontdesk::reservationHistory",["guest"=>$guest,"res"=>$res]);
    }

    public function company($id)
    {
        $company = \Kris\Frontdesk\Company::find($id);
        $res = null;
        if(isset($_GET['startdate']) && isset($_GET['enddate'])){
            $res = \Kris\Frontdesk\Reservation::whereRaw("(date(checkin) between ? and ? or date(checkout) between ? and ?)")->where("company_id",$id)->setBindings([$_GET['startdate'],$_GET['enddate'],$_GET['startdate'],$_GET['enddate'],$id])->get();
        }

        return \View::make("Frontdesk::reservationHistory",["guest"=>$guest,"res"=>$res]);
    }

    public function reservation($id)
    {

    }
}