<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use ORG;
use App\POSReport;
class POSReportController extends Controller
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

       switch ($name) {
            case "Cashier":
                $info = POSReport::CashierBills($range);
                return \View::make("Pos.Reports.CashierBills",$info);
            case "RoomPost":
                $info = POSReport::RoomPosts($range);
                return \View::make("Pos.Reports.RoomPosts",$info);
            case "Credits":
                $info = POSReport::Credits($range);
                return \View::make("Pos.Reports.Credits",$info);
            case "CashierShift":
                $info  = POSReport::CashierShift($range);
                return \View::make("Pos.Reports.CashierShift",$info);
            case "summaryDay":
                $store_id = isset($_GET['store']) ? $_GET['store'] : 0;
                $cashier =  isset($_GET['cashier']) ?  isset($_GET['cashier']) : 0;

                $bills = POSReport::Bills($range,$store_id,$cashier,[\ORG\Bill::PAID,\ORG\Bill::SUSPENDED]);
                $room = POSReport::RoomPostsSummary($range,$store_id);
                $credit = POSReport::CreditsSummary($range,$store_id);

                $data  = ["bills"=>$bills,"room"=>$room,"credits"=>$credit];

             return \View::make("Pos.Reports.Sales",$data);

            case "DailySalesMix":
                $store_id = isset($_GET['store']) ? $_GET['store'] : 0;
                $cashier =  isset($_GET['cashier']) ?  isset($_GET['cashier']) : 0;

                $bills = POSReport::Bills($range,$store_id,$cashier,[\ORG\Bill::PAID,\ORG\Bill::SUSPENDED,\ORG\Bill::CREDIT,\ORG\Bill::OFFTARIFF,\ORG\Bill::ASSIGNED]);

                $room = POSReport::RoomPostsSummary($range,$store_id);

                $credit = POSReport::CreditsSummary($range,$store_id);

                $data  = ["bills"=>$bills,"room"=>$room,"credits"=>$credit];

             return \View::make("Pos.Reports.SalesMix",$data);

            case "MyShiftReport" : 
                $store_id = isset($_GET['store']) ? $_GET['store'] : 0;
                $cashier =  \Auth::user()->id;

                $bills = POSReport::Bills($range,$store_id,$cashier,[\ORG\Bill::PAID,\ORG\Bill::SUSPENDED,\ORG\Bill::CREDIT,\ORG\Bill::OFFTARIFF,\ORG\Bill::ASSIGNED]);
                $room = POSReport::RoomPostsSummary($range,$store_id,$cashier);
                $credit = POSReport::CreditsSummary($range,$store_id,$cashier);

                $data  = ["bills"=>$bills,"room"=>$room,"credits"=>$credit];

                return \View::make("Pos.Reports.CashierSalesMix",$data);

            default:
                return dd("Report Requested does not exist");

       }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function CreditRoomPost(Request $req)
    {
    }

    public function getData($name,Request $req)
    {
    	$date = $req->input("date",\ORG\Dates::$RESTODATE);
	 	$store = $req->input("store",0);
		$cashier = $req->input("cashier",0);

    	switch ($name) {
    		case 'RoomCreditPost':

    		$date = $req->input("date",\ORG\Dates::$RESTODATE);
       	 	$store = $req->input("store",0);
        	$cashier = $req->input("cashier",0);


        $rooms = \DB::select("select room,group_concat(idbills) as billid,group_concat(bill_total) as totals from bills where status=3 and deleted =  0 and date(date)='$date'
            group by room");
        $GuestRoom = array();
        foreach($rooms as $room)
        {
            $GuestRoom[$room->room] = \DB::connection("mysql_book")->select("SELECT Account,concat_ws(' ',Fname,Lname) as guest FROM trans where Rnum =? order by Account desc limit 1",[$room->room]);
        }


        //$data = \DB::select("select sum(bill_total) as total,status from bills where date(bills.date)='$date'  and status in (1,2,3,5) and deleted=0 group by status order by status desc");

        $credits = \DB::select("select customer,group_concat(idbills) as billid,group_concat(bill_total) as totals,username from
                bills
                join users on users.id = user_id
                where status=".\ORG\Bill::CREDIT." and deleted=0 and date(bills.date)='$date'
                group by customer");

        return \View::make("Pos.Reports.RoomCreditPost",["rooms"=>$rooms,"credits"=>$credits,"guest"=>$GuestRoom]);
    			break;


    		//sales details
    			case  "SalesDetails":
    			$date = $req->input("date",\ORG\Dates::$RESTODATE);
	       	 	$store = $req->input("store",0);
	        	$cashier = $req->input("cashier",0);

		       $data = \DB::select("select idbills,customer,product_name,bill_items.unit_price,qty,bill_total,(amount_paid-change_returned) as totalpaid,username from bills
		                    join bill_items on bill_id = idbills
		                    join products on products.id = bill_items.product_id
		                    join users on users.id = bills.user_id where date(bills.date) = ? limit 10",[$date]);

		        $datax = \DB::select("select idbills,customer,group_concat(product_name) as product,group_concat(qty) as quantity,group_concat(unit_price) as unitprice,bill_total as total,(amount_paid-change_returned)as paid,bank_card,cash,check_amount from bills
		            join bill_items on bill_items.bill_id = idbills
		            join products on products.id = bill_items.product_id
                    join payments on payments.bill_id=idbills
		             where status <> 4 and  date(bills.date)=? and deleted=0
		            group by idbills",[$date]);

		        $data = \DB::select("select sum(bill_total) as total,status from bills where date(bills.date)='$date'  and status in (1,2,3,5) and deleted=0 group by status order by status desc");

		        $data2 = \DB::select("select sum(bank_card) as card,sum(cash) as cash from payments where void=0 and date(date) ='$date'")[0];

    			 return \View::make("Pos.Reports.SalesDetails",["data"=>$data,'data2'=>$data2,"bills"=>$datax]);

    		case "ProductsReport":
    			$store_str = "";

    			if($store>0)
    			{
    				$store_str = " and store_id=$store";
    			}

    			$sql = "select product_name,category_id,unit_price,sum(qty) as qty,store_name from bill_items join bills on idbills=bill_id  join products on id=product_id left join categories on categories.id = products.category_id left join store on store.idstore = store_id where deleted=0 and date(bills.date)=? $store_str group by products.id order by store_id";
    			$data= \DB::select($sql,[$date]);

    			$free = \DB::select("select sum(qty*unit_price) as free from bills join bill_items on bill_id=idbills where date(date)=? and deleted=0 and status =".\ORG\Bill::OFFTARIFF."",[$date])[0]->free;

                if($store==3){
                    $free = 0;
                }
                return \View::make("Pos.Reports.ProductsReport",["data"=>$data,"free"=>$free]);
    			break;

    			case "CashierReport":
    			$sql = "select username,sum(bank_card) as card,sum(cash) as cash from payments join users on users.id =user_id where void=0 and date(payments.date)=? group by user_id";
    			$data = \DB::select($sql,[$date]);

    			return \View::make("Pos.Reports.CashierReport",["data"=>$data,"credits"=>""]);
    	}
    }
}
