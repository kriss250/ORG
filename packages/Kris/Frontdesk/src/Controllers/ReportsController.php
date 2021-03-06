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
        $start_date = isset($_GET['startdate']) ? $_GET['startdate'] : \Kris\Frontdesk\Env::WD()->format("Y-m-d");
        $end_date = isset($_GET['enddate']) ? $_GET['enddate'] : \Kris\Frontdesk\Env::WD()->format("Y-m-d");

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
                return \View::make("Frontdesk::reports.Banquet",["orders"=>$orders]);
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

                $data = $frontdesk->receptionist($range,\FO::me()->idusers);
                return \View::make("Frontdesk::reports.MyShift",$data);
            case "receptionist":
                $data = $frontdesk->receptionist($range,0);
                $_data = [];

                $pay = null;
                $sale = null;
                foreach($data["payments"] as $pay)
                {
                    $_data[$pay->username]["payments"][] = $pay;
                    $_data[$pay->username]["currencies"][] = $data['currencies'];
                }

                foreach ($data["sales"]  as $sale)
                {
                	  $_data[$sale->username]["sales"][] = $sale;
                      $_data[$sale->username]["currencies"][] = $data['currencies'];
                }

                return \View::make("Frontdesk::reports.Receptionist",["users"=>$_data]);
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
                $q = "SELECT idmisc_sales,is_credit,guest,currencies.alias,receipt,service,method_name,amount,original_amount,username,misc_sales.date FROM misc_sales
                                join users on users.idusers = user_id
                                left join currencies on currencies.idcurrency = misc_sales.currency
                                left join pay_method on pay_method.idpay_method = pay_mode where date(misc_sales.date) between ? and ?";

                $data = \DB::connection("mysql_book")->select($q,$range);

                return \View::make("Frontdesk::reports.ExtraSales",["sales"=>$data]);

            case "refund" :
                $refunds = \Kris\Frontdesk\Payment::whereBetween(\DB::raw("date(folio.date)"),$range)->where("debit",">","0")->get();
                return \View::make("Frontdesk::reports.Refund",["refunds"=>$refunds]);
            default:
                abort(404);
                break;
        }
    }

    public static function RoomStatusChartJson()
    {
        $sql = "select concat(status_name,' (',count(*),')') as name,(count(*)/(select count(*) from rooms))*100 as y from rooms
        join room_status on rooms.status  = room_status.idroom_status
            group by status order by status_name asc";
        $data  = \DB::connection("mysql_book")->select($sql);
        return json_encode($data,JSON_NUMERIC_CHECK);
    }
}
