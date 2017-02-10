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

    public function saveOrder(Request $req)
    {
        $this->orderDate = \ORG\Dates::$RESTODT;
        $data = $req->all();
        $items =  json_decode($data['data']);

        if($data['waiter_id'] < 1)
        {
            return json_encode(["errors"=>["Unable to save this bill"]]);
        }

        DB::beginTransaction();

        $orderid = DB::table('orders')->insertGetId([
                    "waiter_id"=> $data['waiter_id'],
                    "customer"=>$data['customer'],
                    "user_id"=> \Auth::user()->id,
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

            $xid = gettype($item->idstore)=="object" ? $item->idstore->store_id : $item->idstore;
            array_push($billItems,["order_id"=>$orderid,"product_id"=>$item->id,"unit_price"=>$item->price,"qty"=>$item->qty,"store_id"=>$xid]);
        }

        $_ins = DB::table("bill_items")->insert($orderItems);

        if($_ins>0 && $billid>0)
        {
            DB::commit();
            $res = array(
                        "message"=> $this->pay ? "Bill Payment Saved" : "Bill Saved" ,
                        "idbills"=>$billid,
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
        $id = $req->input("id");

        if(\Auth::user()->level < 7)
        {
            return "0";
        }

        //For suspended bills
        if($req->input("retain",0) == 0){
            try
            {

                DB::transaction(function($id) use($id){
                   // DB::update("delete from bill_items where bill_id=?",[$id]);
                   // DB::delete("delete from bills where idbills=?",[$id]);
                     DB::update("update bills set deleted=?,deleted_by=? where idbills=?",
                        [
                        1,
                        Auth::user()->id,//user
                        $id
                        ]);
                    DB::update("update payments set void=1 where bill_id=?",[$id]);
                });


                \ORG\POS::Log("Deleted Suspended Bill #".$id,"danger");
                return "1";

            }catch (Exception $ex)
            {
                return "0";
            }

        // For paid bills
        }else{


          try
            {
                DB::transaction(function($id) use($id){
                    DB::update("update bills set deleted=?,deleted_by=? where idbills=?",
                        [
                        1,
                        Auth::user()->id,//user
                        $id
                        ]);
                    DB::update("update payments set void=1 where bill_id=?",[$id]);
                });
                //Restore items for paid bills only
                if($id>0)
                {
                    $this->restoreProductsFromStockByBill($id);
                }

                \ORG\POS::Log("Delete Paid Bill #".$id,"danger");
                return "1";

            } catch (Exception $ex)
            {
                return "$ex";
            }
        }

    }


    public function getOrdItems(Request $req)
    {
        $billID = $req->input("billID",0);

        $items = DB::select("select product_name,unit_price,qty from bill_items join products on products.id = product_id where bill_id=?",[$billID]);
        return json_encode($items);

    }

    public function printOrder($id)
    {
        if(!is_numeric($id))
        {
            return "0";
        }

        $sql = "SELECT idbills,format(bill_total,0) as bill_total,format(tax_total,0) as tax_total,amount_paid,change_returned,pay_date,date_format(bills.date,'%d/%m/%Y %T') as date,bills.status,waiter_name,username,product_name,qty,unit_price,(qty*unit_price) as product_total,product_id,EBM,customer,status FROM bills
                join bill_items on bill_id = idbills
                join products on products.id = product_id
                join waiters on idwaiter = waiter_id
                join users on users.id = user_id
                where idbills =? and deleted=0 ".((Auth::user()->level<8) ? " and print_count < 1" :"");

        $bill = \DB::select($sql,[$id]);

        if($bill){
            \DB::update("update bills set print_count=print_count+1,last_printed_by=? where idbills=?",[Auth::user()->id,$id]);

            if(isset($_GET['xml'])){

                $response = \View::make("Pos.BillPrintXml",["bill"=>$bill]);
                return \Response::make($response)->header("Content-Type","text/plain");

            }
            return \View::make("Pos.BillPrint",["bill"=>$bill]);
        }else {
            return "";
        }

    }

   
}
