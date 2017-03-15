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

class BillsController extends Controller
{

    private $billID,$query,$bill_query;
    private $pay = false;
    private $bill_status = \ORG\Bill::SUSPENDED;
    private $card = 0;
    private $cash = 0;
    private $check = 0;
    private $bon = 0;
    private $splitPay = false;
    private $errors = array();
    private $billDate;
    private $restrictedStores = "";

    public function __contruct()
    {
        $this->billDate = \ORG\Dates::$RESTODT;
        $this->restrictedStores =  \Session::get("restricted_stores");
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $cashier =(\Auth::user()->level < 9) ?  \Auth::user()->id : 0;
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

        $bills = \App\POSReport::Bills($params,0,$cashier);

        return \View::make("/Pos/BillList",["bills"=>$bills]);
    }

    /**
     * Summary of updateBill
     * @param Illuminate\Http\Request $req
     * @return mixed
     */
    public function updateBill(Request $req)
    {
        $id = $req->input('bill_id');
        $customer = $req->input("customer");
        $items = json_decode($req->input('itemUpdates'));
        $items_to_update = $items->toUpdate;
        $items_to_delete = $items->toDelete;
        $new_items  = $items->newItems;
        $prvBill = \App\Bill::find($id);
        $updated= 0;
        try {

            $the_bill = \App\Bill::find($id);

            if(strlen($customer)>0)
            {
                $updated = DB::update("update bills set customer=?,last_updated_by=?,last_updated_at=? where idbills=?",
                        [$customer,\Auth::user()->id,\ORG\Dates::$RESTODT,$id]
                    );

            }

            if($the_bill->print_count > 0) return $updated;

            if((count($new_items)+count($items_to_delete)+count($items_to_update))==0){return $updated;}

            //Update
            foreach ($items_to_update as $item) {
                DB::update("update bill_items set unit_price=?,qty=? where bill_id=? and product_id=?",
                    [$item->price,$item->qty,$id,$item->id]
                );

            }

            //Delete
            if(count($items_to_delete)>0){
                DB::delete("delete from bill_items where bill_id=? and product_id in(".implode(',', $items_to_delete).")",
                    [$id]
                );
            }

            //insert
            foreach ($new_items as $item) {
                DB::insert("insert into bill_items (bill_id,product_id,unit_price,qty,store_id) values(?,?,?,?,?)",
                    [$id,$item->id,$item->price,$item->qty,$item->idstore]
                );
            }

            // Update the bill totals

            $updated = DB::update("update bills set last_updated_by=?,last_updated_at=?,bill_total=?,tax_total=? where idbills=?",[
                \Auth::user()->id,\ORG\Dates::$RESTODT,$req->input('billTotal'),$req->input('taxTotal'),$id
            ]);

            \ORG\POS::Log("Update suspended bill #".$id." PRV AMT : {$prvBill->bill_total} New AMT : ".$req->input('billTotal').", Printed : {$prvBill->print_count}","default");

            return $updated;

        }
        catch(\Exception $ex)
        {
            return 0;
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $req)
    {
    }

    public function Pay(Request $req)
    {
        $theBill = json_decode($this->suspend($req,true));
        $billID = $theBill->idBill;
    }

    public function suspend(Request $req,$pay=false)
    {
        $this->billDate = \ORG\Dates::$RESTODT;
        $data = $req->all();
        $items =  json_decode($data['data']);

        $this->bill_status = $pay ? \ORG\Bill::PAID : \ORG\Bill::SUSPENDED;
        if($data['waiter_id'] < 1)
        {
            return json_encode(["errors"=>["Unable to save this bill"]]);
        }

        DB::beginTransaction();

        $billid = DB::table('bills')->insertGetId([
                    "waiter_id"=> $data['waiter_id'],
                    "bill_total"=>$data['billTotal'],
                    "tax_total"=> $data['taxTotal'],
                    "customer"=>$data['customer'],
                    "user_id"=> \Auth::user()->id,
                    "date"=>$this->billDate,
                    "last_updated_by"=>\Auth::user()->id,
                    "status"=>$this->bill_status,
                    //if The bill is at the same time paid
                    "amount_paid"=> isset($data['paid_amount']) ? $data['paid_amount'] : 0 ,
                    "change_returned"=>isset( $data['change_returned']) ?  $data['change_returned'] : 0,
                    "pay_date"=> isset($data['paid_amount']) ? $this->billDate : null
                ]);

        $orders = isset($data['orderids']) ? json_decode($data['orderids']) : [];
        if(count($orders)>0)
        {
            \App\Order::whereIn("idorders",$orders)->update(["has_bill"=>1,"bill_id"=>$billid]);
        }

        $billItems = array();

        foreach($items as $item){

            if($item ==null)
            {
                continue;
            }

            $xid = gettype($item->idstore)=="object" ? $item->idstore->store_id : $item->idstore;
            array_push($billItems,["bill_id"=>$billid,"product_id"=>$item->id,"unit_price"=>$item->price,"qty"=>$item->qty,"store_id"=>$xid]);
        }

        $_ins = DB::table("bill_items")->insert($billItems);

        if($_ins>0 && $billid>0)
        {
            DB::commit();
            $res = array(
                        "message"=> $this->pay ? "Bill Payment Saved" : "Bill Saved" ,
                        "idbills"=>$billid,
                        "date"=> date("d/m/Y H:i:s",strtotime($this->billDate)),
                        "errors"=> array()
                     );

            return json_encode($res);

        }else
        {
            DB::rollBack();
            return json_encode(["idBills"=>0,"errors"=>["Unable to save this bill , please contact your system administrator"]]);
        }

    }

    public function getCustomerByBill($id)
    {
        return \DB::select("select customer,print_count from bills where idbills=?",[$id]);
    }

    /**
     * Summary of Create Bill
     */
    public function createBill()
    {


    }

    public function paySuspendedBill(Request $req)
    {

            $errors = [];

            $data = $req->all();
            $mode = $req->input('mode',null);
            $this->bill_status =  $mode == null ? \ORG\Bill::SUSPENDED : \ORG\Bill::PAID;

            $amount = $data['amountPaid']-($data['amountPaid']-$data['amountDue']);

            switch (strtolower($mode))
            {
                case "debit" :
                    $this->bill_status = \ORG\Bill::PAID ;
                    break;
                case "credit":
                    $this->bill_status = \ORG\Bill::CREDIT;
                    break;
                case "0": //off tariff
                    $this->bill_status = \ORG\Bill::OFFTARIFF;
                    $billinfo['billTotal'] = 0;
                    $billinfo['taxTotal'] = 0;
                    $data['amountPaid'] = 0;
                    $billinfo['change_returned'] = 0;
                    break;
            }


            if(isset($data['splitPayments']))
            {
                $this->card = $data['splitPayments']['bankcard'];
                $this->cash = $data['splitPayments']['cash'];
                $this->splitPay = true;

                //Amount paid must be equal to the total amount of the bill

                if($data['amountPaid']!=$data['amountDue'])
                {
                    array_push($errors,"Split payment total must be equal to the total amount of the bill");
                }

            }else {
                $this->splitPay = false;

                switch (strtolower($req->input('method'))) {
                    case 'cash':
                        $this->cash = $amount;
                        if(strtolower($mode) =="credit")
                        {
                            $this->cash =  $data['amountPaid'];
                        }
                        break;
                    case 'bank card':
                        $this->card = $data['amountPaid'];
                        break;
                    case 'check':
                        $this->check = $amount;
                        break;
                    case 'bon':
                        $this->bon = $amount;
                        break;
                }
            }


            if(!is_numeric($data['amountDue']) || !is_numeric($data['amountPaid']) || !is_numeric($data['billID']) )
            {
                array_push($errors, "Data Format Error");
            }

            if($data['amountPaid']==0 && $this->bill_status == \ORG\Bill::PAID){
                array_push($errors, "Amount paid can not be zero for paid bills");
            }

            if($data['billID']==0){
                array_push($errors, "Invalid Bill identification");
            }

            $customer = "";
            $customerObj = $this->getCustomerByBill($data['billID']);

            $customer = isset($customerObj[0]) ? $customerObj[0]->customer : "";
            $print_count = isset($customerObj[0]) ? $customerObj[0]->print_count : 0;

            if( ($this->bill_status== \ORG\Bill::OFFTARIFF || $this->bill_status== \ORG\Bill::CREDIT ) && strtolower(trim($customer)) == "walkin" )
            {
                array_push($errors, "The name of the customer is required for OFFTARIFF & CREDIT Bills");
            }

            if($this->bill_status== \ORG\Bill::OFFTARIFF && $print_count > 0 )
            {
                array_push($errors, "You cannot mark a bill as OFFTARIFF, when the bill has already been printed.");
            }

            $paid_status = $this->bill_status;
            $change_returned = 0;

            if(count($errors) > 0)
            {
                return json_encode(["errors"=>$errors]);
            }

            if( ($this->splitPay || $paid_status == \ORG\Bill::PAID || (strtolower($req->input('method'))=="bank card") || (strtolower($req->input('method'))=="cash")) && $this->bill_status != \ORG\Bill::OFFTARIFF ){
                //insert into payments

                DB::insert("insert into payments (check_amount,bank_card,cash,bon,bill_id,comment,user_id,date) values(?,?,?,?,?,?,?,?)",
                        [
                            $this->check,$this->card,$this->cash,$this->bon,$data['billID'],$req->input('comment'),Auth::user()->id,\ORG\Dates::$RESTODT
                        ]
                );

                $change_returned = $data['amountPaid']-$data['amountDue'];

                if($paid_status == \ORG\Bill::CREDIT) { $change_returned = 0;}


            }

            $res = DB::update("update bills set last_updated_by=?,last_updated_at=?,status=$paid_status,amount_paid=?,change_returned=?,pay_date=? where idbills=?",[
                \Auth::user()->id,\ORG\Dates::$RESTODT,
                $data['amountPaid'],
                 $change_returned,
                \ORG\Dates::$RESTODT,
                $data['billID']

            ]);


            if(count($errors)==0 && $res > 0){

                \ORG\POS::Log("Paid Bill #".$data['billID'],"default");
                return json_encode(["msg"=>"Payment Saved"]);
            }else {
                array_push($errors,"Unable to save payment");
                return json_encode(["errors"=>$errors]);
            }


    }


    public function restoreProductsFromStockByBill($id)
    {


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

    public function getCurrentSales()
    {
        return json_encode(DB::select("SELECT format( coalesce(sum(cash),0),0) as cash,format(coalesce(sum(bank_card),0),0) as card FROM payments where user_id=? and void=0 and date(date) ='".\ORG\Dates::$RESTODATE."'",[Auth::user()->id])[0]);
    }

    public function getSuspendedBills()
    {

        if(isset($_GET['json'])){

            //Opening A Bill with its items json
            if(isset($_GET['bill_id'])){
                $sql = "SELECT unit_price as price,qty,product_name as name,products.id,(unit_price*qty) as total FROM bill_items join products on products.id = product_id where bill_id=?";
                $items = DB::select($sql,[$_GET['bill_id']]);
                $bill  = DB::select("select bill_total,tax_total,customer,waiter_name,waiter_id,bills.date,shared from bills join waiters on waiters.idwaiter = waiter_id where deleted=0 and idbills=? and (user_id=? or shared=1)",[$_GET['bill_id'],Auth::user()->id])[0];
                return json_encode([$items,$bill]);
            }

            //Load suspended Bills without items
            $sql = "SELECT idbills,bill_total,tax_total,customer,shared,concat_ws(' ',waiters.firstname,waiters.lastname) as waiter_name,waiter_id,bills.date  as date FROM bills
                join waiters on waiters.idwaiter = waiter_id
                where deleted=0 and bills.status=? and (user_id = ? or shared=1)
                order by waiter_id,idbills desc";

            $bills = DB::select($sql,[\ORG\Bill::SUSPENDED,Auth::user()->id]);
            $items = new \stdClass;
            return json_encode([$items,$bills]);
        }
    }

    public function getBillItems(Request $req)
    {
        $billID = $req->input("billID",0);

        $items = DB::select("select product_name,unit_price,qty from bill_items join products on products.id = product_id where bill_id=?",[$billID]);
        return json_encode($items);

    }

    public function printBill($id)
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
                $response = html_entity_decode(trim(\View::make("Pos.BillPrintXml",["bill"=>$bill])));
                return \Response::make($response)->header("Content-Type","text/plain");

            }
            return \View::make("Pos.BillPrint",["bill"=>$bill]);
        }else {
            return "";
        }

    }

    public function assignBill(Request $req)
    {

        $data['Room'] = $req->input("Room");
        $data['BillID'] = $req->input("BillID");
        $data['due'] = $req->input("due");
        $status['occupied'] = 2; //\ORG\RoomStatus::$OCCUPIED;
        $status['assigned'] = 3; //\ORG\Bill::$ASSIGNED;

        $date = \ORG\Dates::$RESTODATE;
        $resto_code = 2;
        $bar_code=3;

        $resto_motif = "Resto Bill ".$data['BillID'];
        $bar_motif = "Bar Bill ".$data['BillID'];

        try {

            DB::beginTransaction();

            $bill_total_sql = "select sum(qty*unit_price) as amount,bill_items.store_id from bill_items
            join products on products.id = product_id
            join categories on category_id = categories.id
            where bill_id =?
            group by bill_items.store_id";

            $the_bill = DB::select($bill_total_sql,[$data['BillID']]);


            $v = DB::connection("mysql_book")->select("select idrooms,companies.name as company,idreservation as reservation_id,concat_ws(' ',firstname,lastname) as guest from reservations
                join rooms on rooms.idrooms = room_id
                join guest on guest.id_guest = guest_id
                left join companies on companies.idcompanies = company_id
                where checked_in is not null and checked_out is null and room_number =?",[$data['Room']]);
            //bar = 1
            if($v){
               $res= $v[0]->reservation_id;
               $customer_name = $v[0]->guest;
               $room_id = $v[0]->idrooms;
               $cp = $v[0]->company;
               foreach($the_bill as $bill){

                   $ins = DB::connection("mysql_book")->insert("insert into room_charges (room_id,reservation_id,charge,amount,motif,date,user_id,user,pos) values (?,?,?,?,?,?,?,?,?)",
                            [
                                $room_id,
                                $res,
                                $bill->store_id=="1" ||  $bill->store_id=="2" ? $bar_code :  $resto_code,
                                $bill->amount,
                                $bill->store_id=="1" ||  $bill->store_id=="2" ? $bar_motif : $resto_motif,
                                \ORG\Dates::$RESTODT,
                                1,
                                \Auth::user()->username,
                                1
                        ]);

               }


               //Update account
               DB::connection("mysql_book")->update("update reservations set due_amount = due_amount+? where idreservation=?",[
                    $data['due'],
                    $res
                   ]);

               if($ins)
               {
                   if(DB::update("update bills set  company=?,last_updated_by=?,last_updated_at=?,customer=?,status=?,room=? where idbills=?",[$cp,\Auth::user()->id,\ORG\Dates::$RESTODT,$customer_name,$status['assigned'],$data['Room'],$data['BillID']])){
                       DB::commit();
                       return "1";
                   }
               }else {
                   DB::rollBack();
                   return "0";
               }
            }else {
                DB::rollBack();
                return "0";
            }
        }catch( Exception $ex)
        {
            DB::rollBack();
            return  $ex;
        }

        /*
        try {

            DB::transaction(function() use (&$data,&$status,&$date){
                //Get room info
                $room_info = DB::connection("mysql")->select("
                    select idrooms,reservation_id from rooms
                    join reserved_rooms on room_id=idrooms
                    where room_number= ? and status=? order by reservation_id desc limit 1
                    ",[$data['Room'],$status['occupied']] )[0];

                //insert into room charges
                $in = DB::connection("mysql")->insert("insert into room_charges (room_id,charge,user_id,amount,motif,date,reservation_id) values(?,?,?,?,?,?,?)",[
                    $room_info->idrooms,
                    Auth::user()->id,
                    1,
                    $data['due'],
                    'R/B Charges #'.$data['BillID'],
                    $date,
                    $room_info->reservation_id
                ]);

                //Update account dues
                $update = DB::connection("mysql")->update("update accounts set due_amount=due_amount+".$data['due']. " where reservation_id=".$room_info->reservation_id);

                if($in > 0 && $update > 0)
                {
                    DB::insert("update bills set status=?,room=? where idbills=?",[$status['assigned'],$data['Room'],$data['BillID']]);

                }else {
                    return false;
                }

            });

            return '1';

        }catch(Exception $ex){

            return "0";
        }

        */
    }

    public function payCreditBill(Request $req)
    {
        $data = $req->all();
        $errors = array();
        $amount_due= 0;
        $res=0;
        $billdata = DB::select("SELECT bill_total,status,amount_paid FROM org_pos.bills where idbills =? limit 1",[$data['billID']])[0];
        $amount_due = $billdata->bill_total-$billdata->amount_paid;
        $settled = $billdata->amount_paid;

        $newamount = $settled+$data['amount_paid'];

        $amount = $data['amount_paid'];

            switch (strtolower($req->input('method'))) {
                case 'cash':
                    $this->cash = $amount;
                    break;
                case 'bank card':
                    $this->card = $amount;
                    break;
                case 'check':
                    $this->check = $amount;
                    break;
                case 'bon':
                    $this->bon = $amount;
                    break;
            }

            if(!is_numeric($data['amount_paid']))
            {
                array_push($errors, "Data Format Error");
            }

            if($billdata->status!=\ORG\Bill::CREDIT)
            {
                //array_push($errors, "Unable to process payment ,The bill is not marked as credit");
            }

            if($data['amount_paid']==0){
                array_push($errors, "Amount can not be zero");
            }

            if($data['billID']==0){
                array_push($errors, "Invalid Bill identification");
            }

            if($data['amount_paid']>$amount_due)
            {
                array_push($errors, "Amount paid must be less or equal to ".$amount_due);
            }

            $paid_status =\ORG\Bill::PAID;

            if($amount_due>$data['amount_paid'])
            {
                $paid_status = \ORG\Bill::CREDIT;
            }

            if(empty($errors)){
                //insert into payments
                DB::insert("insert into payments (check_amount,bank_card,cash,bon,bill_id,comment,user_id,date) values(?,?,?,?,?,?,?,?)",
                        [
                            $this->check,$this->card,$this->cash,$this->bon,$data['billID'],$req->input('comment'),Auth::user()->id,\ORG\Dates::$RESTODT
                        ]
                );

                $res = DB::update("update bills set last_updated_by=?,last_updated_at=?,amount_paid=amount_paid+?,change_returned=?,pay_date=?,status=? where idbills=?",[
                    \Auth::user()->id,\ORG\Dates::$RESTODT,
                    $data['amount_paid'],
                    0,
                    \ORG\Dates::$RESTODT,
                    $paid_status,
                    $data['billID']

                ]);
            }

            return json_encode(array("pay"=>$res,"errors"=>$errors));
    }

    public function payAssignedBill(Request $req)
    {
        $data = $req->all();
        $errors = array();
        $res = 0;
        $amount = $data['amount_paid'];
        $billdata = DB::select("SELECT bill_total,status,amount_paid FROM org_pos.bills where idbills =? limit 1",[$data['billID']])[0];
        $amount_due= 0;
        $amount_due = $billdata->bill_total-$billdata->amount_paid;
        $settled = $billdata->amount_paid;
        $newamount = $settled+$data['amount_paid'];

            switch (strtolower($req->input('method'))) {
                case 'cash':
                    $this->cash = $amount;
                    break;
                case 'bank card':
                    $this->card = $amount;
                    break;
                case 'check':
                    $this->check = $amount;
                    break;
                case 'bon':
                    $this->bon = $amount;
                    break;
            }

            if(!is_numeric($data['amount_paid']))
            {
                array_push($errors, "Data Format Error");
            }

            if($data['amount_paid']==0){
                array_push($errors, "Amount can not be zero");
            }

            if($data['billID']==0){
                array_push($errors, "Invalid Bill identification");
            }

            if($data['amount_paid']>$amount_due)
            {
                array_push($errors, "Amount paid cannot be greater than the bill amount");
            }

            $paid_status =\ORG\Bill::PAID;


            if($amount_due>$data['amount_paid'])
            {
                $paid_status = \ORG\Bill::CREDIT;
            }

            if(empty($errors)){
                //insert into payments
                DB::insert("insert into payments (check_amount,bank_card,cash,bon,bill_id,comment,user_id,date) values(?,?,?,?,?,?,?,?)",
                        [
                            $this->check,$this->card,$this->cash,$this->bon,$data['billID'],$req->input('comment'),Auth::user()->id,\ORG\Dates::$RESTODT
                        ]
                );

                $res = DB::update("update bills set last_updated_by=?,last_updated_at=?,amount_paid=amount_paid+?,change_returned=?,pay_date=?,status=? where idbills=?",[
                   \Auth::user()->id,\ORG\Dates::$RESTODT,
                    $data['amount_paid'],
                    0,
                    \ORG\Dates::$RESTODT,
                    $paid_status,
                    $data['billID']

                ]);
            }
            return json_encode(array("pay"=>$res,"errors"=>$errors));
    }

    public function assignedList(Request $req)
    {
        $join = "join bill_status on status_code = bills.status join users on users.id = bills.user_id join waiters on idwaiter=waiter_id ";

        if(isset($_GET['json'])){

            $columns = array(
                array( 'db' => 'idbills', 'dt' => 0 ),
                array( 'db' => 'room', 'dt' => 1 ),
                array( 'db' => 'bill_total',  'dt' => 2 ),
                array( 'db' => 'tax_total',   'dt' => 3 ),
                array( 'db' => "concat_ws('/',amount_paid,change_returned)",'ds'=> "amount_paid",  'dt' => 4 ),
                array( 'db' => '(amount_paid-change_returned)','dt' => 5,'ds'=> "amount_paid"),
                array( 'db' => 'username','dt' => 6),
                array( 'db' => 'waiter_name','dt' => 7),
                array( 'db' => 'pay_date','dt' => 8,
                        'formatter'=>function($d,$row){
                            if(is_null($d)){
                                return "-";
                            }
                            return date(ORG\Dates::DSPDATEFORMAT,strtotime($d));
                        }
                    ),
                 array( 'db' => 'bills.date','dt' => 9,
                    'formatter'=>function($d,$row){
                        return date(ORG\Dates::DSPDATEFORMAT,strtotime($d));
                    }
                )
            );

            return Datatables\SSP::simple( $_GET, "bills", "idbills", $columns,$join,"","bills.status=3 and deleted =0" );

        }

        return \View::make("/Pos/assignedBills");
    }

    public function deleteBillPayments()
    {
        $id = $_GET['id'];

        \DB::beginTransaction();
        $b = \DB::update("update bills set last_updated_by=?,last_updated_at=?,status=?, amount_paid=?,change_returned=?, pay_date=? where idbills=? and date(bills.date)=?",[\Auth::user()->id,\ORG\Dates::$RESTODT,\ORG\Bill::SUSPENDED,0,0,null,$id,\ORG\Dates::$RESTODATE]);

        $p = \DB::update("update payments set void=1 where idpayments>0 and bill_id=?",[$id]);

        if($b > 0 && ($p>0 || $_GET['ignore']=="1"))
        {
            \DB::commit();
            return "1";

        }else {
            \DB::rollBack();
            return "0";
        }

    }

    public function shareBill()
    {
        if(isset($_GET['id']))
        {
            $id= $_GET['id'];
            if(isset($_GET['shared']) && $_GET['shared']=="0"){
                return \DB::update("update bills set shared=1 where idbills=?",[$id]);
            }else {
                return \DB::update("update bills set shared=0 where idbills=?",[$id]);
            }

        }
    }

    public function checkRoom()
    {
         if(isset($_GET['room']))
         {
             $data = \DB::connection("mysql_book")->select("select  concat_ws(' ', firstname,lastname) as guest,package_name from rooms join reservations
                on reservations.room_id = idrooms
                join guest on guest.id_guest = guest_id
                where reservations.status = 5 and room_number=? and checked_out is null and checked_in is not null limit 1",[$_GET['room']]);

             echo json_encode($data);

         }
    }
}
