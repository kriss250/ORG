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
use \Kris\Frontdesk;

class ReportsController extends Controller
{
    public function index($name)
    {
        $start_date = isset($_GET['startdate']) ? $_GET['startdate'] : \ORG\Dates::$RESTODATE;
        $end_date = isset($_GET['enddate']) ? $_GET['enddate'] : \ORG\Dates::$RESTODATE;

        $range = [$start_date,$end_date];
        $frontdesk = new \App\FrontofficeReport();

        switch ($name) {
            //Frontdesk Reports
            case "frontdeskDailySales":
                $sales = $frontdesk->Sales($range);
                return \View::make("Frontdesk::reports.Sales",$sales);

            case "frontdeskServiceSales":
                $sales = $frontdesk->ServiceSales($range);
                return \View::make("Frontdesk::reports.ServiceSales",$sales);
            case "frontofficeControl":
                $sales = $frontdesk->OfficeControl($range);
                return \View::make("Frontdesk::reports.OfficeControl",$sales);
            case "frontofficePayment":
                $payments= $frontdesk->PaymentControl($range);
                return \View::make("Frontdesk::reports.PaymentControl",$payments);
            case "foPayments":
                $payments= $frontdesk->Payments($range);
                return \View::make("Frontdesk::reports.FoPayments",$payments);
            case "frontofficeBreakfast":
                $breakfast =  $frontdesk->Breakfast($range);
                return \View::make("Frontdesk::reports.Breakfast",$breakfast);
            case "frontofficeArrival":
                $arrivals =  $frontdesk->arrival($range);
                return \View::make("Frontdesk::reports.Arrival",$arrivals);

            case "frontofficeExpectedArrival":
                $arrivals =  $frontdesk->arrival($range,true);
                $arrivals["expected"] =1;
                return \View::make("Frontdesk::reports.Arrival",$arrivals);
            case "frontofficeDeparture":
                $departure = $frontdesk->Departure($range);
                return \View::make("Frontdesk::reports.Departure",$departure);
            case "frontofficeExpectedDeparture":
                $departure = $frontdesk->Departure($range,true);
                $departure["expected"] = 1;
                return \View::make("Frontdesk::reports.Departure",$departure);
            case "frontdeskMorning":
                $data =  $frontdesk->Morning($range);
                return \View::make("Frontdesk::reports.Morning",$data);
            case "roomtransfers":
                $data= $frontdesk->RoomTransfers($range);
                return \View::make("Frontdesk::reports.Transfers",$data);
            case "rooming":
                $sales = $frontdesk->Rooming($range);
                return \View::make("Frontdesk::reports.Rooming",$sales);
            case "banquet":
                $orders =$frontdesk->banquetOrders($range);
                return \View::make("Frontdesk::reports.Banquet",$orders);
            case "banquetBooking":
                $orders = $frontdesk->banquetBooking($range);
                $banquets = $frontdesk->allBanquets();
                return \View::make("Frontdesk::reports.BanquetBooking",["orders"=>$orders,"banquets"=>$banquets,"range"=>$range]);
            case "foDeposits":
                $deposits = $frontdesk->Deposit($range);
                return \View::make("Frontdesk::reports.Deposits",$deposits);

            case "foLogs":
                $cashier=0;
                if(isset($_GET['cashier']) && is_numeric($_GET['cashier']) &&  $_GET['cashier']>0 )
                {
                    $cashier = $_GET['cashier'];
                }
                $logs= $frontdesk->logs($range,$cashier);
                return \View::make("Frontdesk::reports.Logs",["logs"=>$logs]);

            case "myShift":

                $data= $frontdesk->receptionist($range,\FO::me()->idusers);
                return \View::make("Frontdesk::reports.MyShift",$data);
            case "roomStatus":
                $rooms = \Kris\Frontdesk\Room::all();
                $data =  ["rooms"=>$rooms];
                return \View::make("Frontdesk::reports.RoomStatus",$data);

            case "laundry":
                $orders = \Kris\Frontdesk\Laundry::whereBetween("laundry.date",$range)->get();
                return \View::make("Frontdesk::reports.Laundry",["orders"=>$orders]);

            case "housekeeping":
                $tasks = \Kris\Frontdesk\Housekeeping::where("date",$range[0])->get();
                return \View::make("Frontdesk::reports.Housekeeping",["tasks"=>$tasks]);

            case "extraSales":
                $q = "SELECT idmisc_sales,is_credit,guest,receipt,service,method_name,amount,username,misc_sales.date FROM misc_sales
                                join users on users.idusers = user_id
                                left join pay_method on pay_method.idpay_method = pay_mode where date(misc_sales.date) between ? and ?";

                $data = \DB::connection("mysql_book")->select($q,$range);

                return \View::make("Frontdesk::reports.ExtraSales",["sales"=>$data]);
            default:
                abort(404);
                break;
        }
    }
}