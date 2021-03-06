<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use \ORG;
use \Datatables;
use Exception;
use \Auth;

class OrdersController extends Controller
{

    private $orderID,$query,$bill_query;

    public function __contruct()
    {
        $this->orderDate = \ORG\Dates::$RESTODT;
        $this->restrictedStores =  \Session::get("restricted_stores");

    }

    public function index()
    {
        $params =[\ORG\Dates::$RESTODATE,\ORG\Dates::$RESTODATE];

        if(\Auth::user()->level < 9 ){
            //Prevent viewing past bills (cashiers)
            $params = [\ORG\Dates::$RESTODATE,\ORG\Dates::$RESTODATE];
        }else {
            if(isset($_GET['startdate']))
            {
                $params = [$_GET['startdate'],$_GET['startdate']];
            }
        }
        $waiter = 0;

        if(isset($_GET['waiter'])) {$waiter = $_GET['waiter'];}
        $bills = \App\POSReport::Orders($params,0,$waiter);

        return \View::make("/Pos/OrderList",["bills"=>$bills]);
    }

    public function saveOrder(Request $req)
    {
        $this->orderDate = \ORG\Dates::$RESTODT;
        $data = $req->all();
        $items =  json_decode($data['data']);

        if($data['waiter_id'] < 1)
        {
            return json_encode(["errors"=>["Unable to save this order : Invalid Waiter"]]);
        }

        $waiter = \App\Waiter::where("idwaiter",$data['waiter_id'])->where("pin",md5($data['waiter_pin']))->get()->first();

        if($waiter == null)
        {
            return json_encode(["errors"=>["Wrong Username/Password "]]);
        }

        $data['waiter_id'] =  $waiter->idwaiter;

        DB::beginTransaction();

        $orderid = DB::table('orders')->insertGetId([
                    "waiter_id"=> $data['waiter_id'],
                    "table_id"=>$data['table_id'],
                    "stock"=>$data['stock'],
                    "date"=>$this->orderDate,
                ]);

        $orderItems = array();

        foreach($items as $item){

            if($item ==null)
            {
                continue;
            }

            //$xid = gettype($item->idstore)=="object" ? $item->idstore->store_id : $item->idstore;
            array_push($orderItems,["order_id"=>$orderid,"product_id"=>$item->id,"unit_price"=>$item->price,"qty"=>$item->qty,"store_id"=>$data['stock'],"side_dishes"=>$item->sideOrders]);
        }

        $_ins = DB::table("order_items")->insert($orderItems);

        if($_ins>0 && $orderid>0)
        {
            DB::commit();
            $res = array(
                        "message"=>"Order Saved" ,
                        "idorders"=> $orderid,
                        "date"=> date("d/m/Y H:i:s",strtotime($this->orderDate)),
                        "errors"=> array()
                     );

            return json_encode($res);

        }else
        {
            DB::rollBack();
            return json_encode(["idBills"=>0,"errors"=>["Unable to save this bill , please contact your system administrator"]]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id,Request $req)
    {

    }


    public function getOrderItems(Request $req)
    {
        $billID = $req->input("billID",0);

        $items = DB::select("select product_name,unit_price,qty from bill_items join products on products.id = product_id where bill_id=?",[$billID]);
        return json_encode($items);

    }

    public function printOrder($id)
    {
        $waiter = \App\Waiter::where("idwaiter",$_GET['waiter'])->where("pin",md5($_GET['pin']))->get()->first();

        if($waiter == null)
        {
            return 0;
        }

        if(!is_numeric($id))
        {
            return "0";
        }

        $sql = "SELECT idorders,date_format(orders.date,'%d/%m/%Y %T') as date,side_dishes,waiter_name,product_name,qty,unit_price,(qty*unit_price) as product_total,product_id,store_name,table_name
                FROM orders
                join order_items on order_id = idorders
                join products on products.id = product_id
                join waiters on idwaiter = waiter_id
                join tables on tables.idtables = table_id
                join store on store.idstore = orders.stock
                where idorders=? and has_bill = 0 ";

        $order = \DB::select($sql,[$id]);

        if($order != null){

            if(isset($_GET['xml'])){

                $response = \View::make("Pos.OrderPrintXml",["order"=>$order]);
                return \Response::make($response)->header("Content-Type","text/plain");
            }
        }else {
            return "";
        }

    }

    public function getOrders()
    {
        return \App\Order::where("has_bill","0")->where("deleted","0")->where(\DB::raw("date(date)"),\ORG\Dates::$RESTODATE)->get()->load("waiter")->load("table");
    }

    public function getOrder()
    {

        $order = \App\Order::where("idorders",$_GET['id'])->where("has_bill","0")->where("deleted","0")->where(\DB::raw("date(date)"),\ORG\Dates::$RESTODATE)->get()->first();
        if($order == null) return [];
        $order->load("waiter");
        $data=  ["order"=>$order,
            "items"=> $order != null ? $order->items->load("product")->all() : []
            ];
        return $order;
    }

    public function delete()
    {
        if(isset($_GET['id']) && is_numeric($_GET['id']))
        {
            echo \App\Order::where("idorders",$_GET['id'])->whereNull("bill_id")->update(["deleted"=>"1","deleted_by"=>\Auth::user()->id]);
        }
    }

    public function myTables(Request $req){
        $waiter = $req->input("waiter",0);
        $pin = $req->input("pin");
        $w = \App\Waiter::where("pin",md5($pin))->where("idwaiter",$waiter)->first();
        if($w == null) return ["error"=>"Invalid waiter"];
        return \App\Order::where("deleted","0")->where("has_bill",0)->where("waiter_id",$waiter)->where(\DB::raw("date(date)"),\ORG\Dates::$RESTODATE)->groupBy("table_id")->get()->load("table");
    }

    public function tableOrders(Request $req)
    {
        $waiter = $req->input("waiter",0);
        $table = $req->input("table");

        $orders = \App\OrderItem::select("order_id")
            ->join("orders","idorders","=","order_id")
            ->where("deleted","0")->where("waiter_id",$waiter)
            ->where("table_id",$table)
            ->where(DB::raw("qty-billed_qty"),">","0")
            ->where(\DB::raw("date(date)"),\ORG\Dates::$RESTODATE)
            ->groupBy("order_id")
            ->get()->toArray();
        $orderIds = [];
        foreach($orders as $o)
        {
            $orderIds[]  = $o["order_id"];
        }

        return \App\Order::with("items.product")->whereIn("idorders",$orderIds)->get();
    }

    public function saveBill(Request $req)
    {
        $data =  $req->input("data",null);
        $data = json_decode($data);

        $cwaiter= \App\Waiter::where("idwaiter", $_POST['waiter']['id'])->where("pin",md5($_POST['waiter']['pin']))->first();

        if($cwaiter == null){
            return  ["success"=>0,"bill"=>0,"error"=>"Invalid Waiter"];
        }
        DB::beginTransaction();

        $resp =["success"=>0,"bill"=>0];


            $bill = new \App\Bill();
            $bill->waiter_id = $_POST['waiter']['id'];
            $bill->status = \ORG\Bill::SUSPENDED;
            $bill->customer = "Walkin";
            $bill->date = \ORG\Dates::$RESTODATE." ".date("H:i:s");
            $bill->is_fixed_discount = $_POST['discount']['type'] == 'fixed';
            $bill->discount  = floatval($_POST['discount']['value']);

            $items = new \Illuminate\Support\Collection();

            foreach($data->billItems as $item) {

                $items->push(new \App\BillItem([
                    "product_id"=>$item->id,
                    "qty"=>$item->qty,
                    "store_id"=> isset($item->store_id) ? $item->store_id: 0 ,
                    "unit_price"=>$item->price
                    ]));

                $bill->bill_total += $item->qty*$item->price;
            }

            $orderItem = null;

            foreach($data->orderItems as $oitem)
            {
                $orderItem = \App\OrderItem::find($oitem->itemid);
                $orderItem->billed_qty +=(($orderItem->billed_qty +$oitem->qty) <= $orderItem->qty ) ?  $oitem->qty : 0;
                $orderItem->save();
            }
            $bill->save();
            $bill->items()->saveMany($items);


            DB::commit();

            $resp = ["success"=>1,"bill"=>$bill->idbills];

            DB::rollBack();
        return $resp;
    }
}
