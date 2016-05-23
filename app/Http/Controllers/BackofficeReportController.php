<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use ORG;
use App\POSReport;
class BackofficeReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($name)
    {
        $start_date = isset($_GET['startdate']) ? $_GET['startdate'] : \ORG\Dates::$RESTODATE;
        $end_date = isset($_GET['enddate']) ? $_GET['enddate'] : \ORG\Dates::$RESTODATE;

        $range = [$start_date,$end_date];
        $frontdesk = new \App\FrontofficeReport();

        switch ($name) {
            case 'dailySales':
                $cashier_id = isset($_GET['cashier']) ? $_GET['cashier'] : 0;
                $store_id =  isset($_GET['store']) ? $_GET['store'] : 0;
                $info = POSReport::Sales($range,$cashier_id,$store_id);
                return \View::make("Backoffice.Reports.POS.Sales",$info);

            case "roomPosts":
                 $info = POSReport::RoomPosts($range);

                return \View::make("Backoffice.Reports.POS.RoomPosts",$info);
            case "debts":
            $info = POSReport::Credits($range);
                return \View::make("Backoffice.Reports.POS.Credits",$info);
            case "cashierBills":
                 $info = POSReport::CashierBills($range);
                return \View::make("Backoffice.Reports.POS.CashierBills",$info);
            case "cancelledBills" :
                $info = POSReport::CancelledBills($range);
                return \View::make("Backoffice.Reports.POS.CancelledBills",$info);
            case "offtariffBills" :
                $info = POSReport::Bills($range,0,0,[\ORG\Bill::OFFTARIFF]);
                return \View::make("Backoffice.Reports.POS.BillList",["bills"=>$info]);
            case "reprintedBills":
                $info = POSReport::ReprintedBills($range);
                return \View::make("Backoffice.Reports.POS.ReprintedBills",$info);
            case "fullDay":
                $cashier_id = isset($_GET['cashier']) ? $_GET['cashier'] : 0;
                $sales = POSReport::Sales($range,$cashier_id);
                $roomposts = POSReport::RoomPosts($range);
                $credits = POSReport::Credits($range);

                return \View::make("Backoffice.Reports.POS.FullDay",["sales"=>$sales,"room_posts"=> $roomposts,"credits"=> $credits]);
            case "summaryDay":
                $store_id = isset($_GET['store']) ? $_GET['store'] : 0;
                $sales = \App\POSReport::SalesSummary($range,$store_id);
                $room =  \App\POSReport::RoomPostsSummary($range,$store_id);
                $credit =  \App\POSReport::CreditsSummary($range,$store_id);

                $data  = ["sales"=>$sales,"room"=>$room,"credits"=>$credit];
                return \View::make("Backoffice.Reports.POS.DaySummary",$data);
                break;
            case "productSales":
                $store = isset($_GET['store']) ? $_GET['store'] : 0;
                $info = POSReport::Products($range,$store);
                return \View::make("Backoffice.Reports.POS.Products",$info);
            case "jsonProducts":
                return POSReport::jsonProducts();
            case "cashierShift":
                $info  = POSReport::CashierShift($range);
                return \View::make("Backoffice.Reports.POS.CashierShift",$info);
            case "StockPOSRelation":
                $data = POSReport::StockPOSRelation();
                return \View::make("Backoffice.Reports.POS.StockPOSRelation",$data);

            case "stockQuantity":
                $id = isset($_GET['warehouse']) ? $_GET['warehouse'] : "0";
                $info  = \App\StockReport::StockQuantity($id);
                return \View::make("Backoffice.Reports.Stock.StockQuantity",$info);

            case "stockRequisition":
                $data = \App\StockReport::Requisitions($range);
                return \View::make("Backoffice.Reports.Stock.Requisitions",$data);
            case "damagedProducts":
                $info = \App\StockReport::DamagedProducts($range);
                return \View::make("Backoffice.Reports.Stock.DamagedProducts",$info);
            case "purchases" :
                $stock = 0 ;
                if(isset($_GET['warehouse']))
                {
                    $stock = $_GET['warehouse'];
                }
                $info = \App\StockReport::Purchases($range,$stock);
                $warehouses = \App\StockReport::getWarehouses();
                $info['warehouses'] = $warehouses;
                return \View::make("Backoffice.Reports.Stock.Purchases",$info);
            case "stockSales":
                $stock = 0 ;
                if(isset($_GET['warehouse']))
                {
                    $stock = $_GET['warehouse'];
                }
                $info = \App\StockReport::Sales($range,$stock);
                $warehouses = \App\StockReport::getWarehouses();
                $info['warehouses'] = $warehouses;

                return \View::make("Backoffice.Reports.Stock.Sales",$info);
            case "purchaseItems":
            $info = \App\StockReport::PurchaseItems($_GET['id']);
                return \View::make("Backoffice.Reports.Stock.PurchaseItems",$info);
            case "saleItems":
                $info = \App\StockReport::SaleItems($_GET['id']);
                return \View::make("Backoffice.Reports.Stock.SaleItems",$info);
            case "stockOverview" :
                $stock = 0 ;
                if(isset($_GET['warehouse']))
                {
                    $stock = $_GET['warehouse'];
                }

                if(!is_numeric($stock))
                {
                    $stock =0;
                }

                $info = \App\StockReport::StockOverview($range,$stock);
                $warehouses = \App\StockReport::getWarehouses();
                $info['warehouses'] = $warehouses;

                return \View::make("Backoffice.Reports.Stock.Overview",$info);
            case "cashBooks":
                $cashbook_id = isset($_GET['cashbook']) ? $_GET['cashbook'] : 3 ;
                $date1 = isset($range[0]) ? $range[0] : date("Y-m-d");
                $date2 = isset($range[1]) ? $range[1] : date("Y-m-d");

                $date = new \DateTime($date1);
                $date->sub(new \DateInterval('P1D'));
                $prev_date = $date->format('Y-m-d');

                $in= 0;
                $out = 0;

                $in_out = \DB::connection("mysql_backoffice")->select("select type,coalesce(sum(amount),0) as amt from cashbook_transactions where deleted=0 and date(date)<=? and cashbook_id=? group by type",[ $prev_date,  $cashbook_id]);

                foreach($in_out as $xc)
                {
                    $in = $xc->type == "IN" ? $xc->amt : $in;
                    $out = $xc->type == "OUT" ? $xc->amt :  $out;
                }

                $initial = $in-$out;




                $cashbook_name = \DB::connection("mysql_backoffice")->select("SELECT cashbook_name FROM cash_book where cashbookid=?",[$cashbook_id]);
                $cashbook_name = isset($cashbook_name[0]->cashbook_name) ?  $cashbook_name[0]->cashbook_name : "";
                $where = "";
                $params = [$cashbook_id];
                if(isset($_GET['date']) && strlen($_GET['date'])>2)
                {
                    $where = " and date(cashbook_transactions.date)=?";
                    array_push($params,$date1);
                }else {
                    $where = " and date(cashbook_transactions.date) between ? and ?";
                    array_push($params,$date1);
                    array_push($params,$date2);
                }



                $transactions = \DB::connection("mysql_backoffice")->select("select transactionid,new_balance,type,amount,motif,username,cashbook_transactions.date from cashbook_transactions join org_pos.users on org_pos.users.id = user_id where cancelled=0 and cashbook_id=? and deleted=0 $where order by transactionid asc",$params);

                return \View::make("Backoffice.Reports.Cashbooks",["book_name"=> $cashbook_name,"transactions"=>$transactions,"initial"=>$initial]);

                //Frontdesk Reports

            case "frontdeskDailySales":
                $sales = $frontdesk->Sales($range);
                return \View::make("Backoffice.Reports.Frontdesk.Sales",$sales);

            case "frontdeskServiceSales":
                $sales = $frontdesk->ServiceSales($range);
                return \View::make("Backoffice.Reports.Frontdesk.ServiceSales",$sales);
            case "frontofficeControl":
                $sales = $frontdesk->OfficeControl($range);
                return \View::make("Backoffice.Reports.Frontdesk.OfficeControl",$sales);
            case "frontofficePayment":
                $payments= $frontdesk->PaymentControl($range);
                return \View::make("Backoffice.Reports.Frontdesk.PaymentControl",$payments);
            case "foPayments":
                $payments= $frontdesk->Payments($range);
                return \View::make("Backoffice.Reports.Frontdesk.FoPayments",$payments);
            case "frontofficeBreakfast":
                $breakfast =  $frontdesk->Breakfast($range);
                return \View::make("Backoffice.Reports.Frontdesk.Breakfast",$breakfast);
            case "frontofficeArrival":
                $arrivals =  $frontdesk->arrival($range);
                return \View::make("Backoffice.Reports.Frontdesk.Arrival",$arrivals);

            case "frontofficeExpectedArrival":
                $arrivals =  $frontdesk->arrival($range,true);
                return \View::make("Backoffice.Reports.Frontdesk.Arrival",$arrivals);
            case "frontofficeDeparture":
                $departure = $frontdesk->Departure($range);
                return \View::make("Backoffice.Reports.Frontdesk.Departure",$departure);
            case "frontofficeExpectedDeparture":
                $departure = $frontdesk->Departure($range,true);
                return \View::make("Backoffice.Reports.Frontdesk.Departure",$departure);
            case "frontdeskMorning":
                $data =  $frontdesk->Morning($range);
                return \View::make("Backoffice.Reports.Frontdesk.Morning",$data);
            case "roomtransfers":
                $data= $frontdesk->RoomTransfers($range);
                return \View::make("Backoffice.Reports.Frontdesk.Transfers",$data);
            case "rooming":
                $sales = $frontdesk->Rooming($range);
                return \View::make("Backoffice.Reports.Frontdesk.Rooming",$sales);
            case "banquet":
                $orders =$frontdesk->banquetOrders($range);
                return \View::make("Backoffice.Reports.Frontdesk.Banquet",$orders);
            case "foDeposits":
                $deposits = $frontdesk->Deposit($range);
                return \View::make("Backoffice.Reports.Frontdesk.Deposits",$deposits);
            default:
                abort(404);
                break;
        }
    }
}
