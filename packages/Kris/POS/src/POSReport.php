<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Carbon\Carbon;

class POSReport extends Model
{
    public static function RoomPosts($date,$store=0,$cashier=0)
    {

        $rooms = \DB::select("select customer,room,group_concat(idbills) as billid,group_concat(bill_total) as totals from bills where status=3 and deleted =  0 and (date(date) between ? and ?) group by room",$date);

        $items = array();
        foreach($rooms as $room)
        {
            $items[$room->room] = \DB::select("select bill_id,group_concat(product_name,':',unit_price,':',qty) as item from bill_items join products on products.id = product_id where bill_id in (".$room->billid.") group by bill_id ");
        }


        return ["rooms"=>$rooms,"items"=>$items];
    }

    public static function RoomPostsSummary($date,$store=0,$cashier=0)
    {
        $store_str  = "";
        $cashier_str = "";

        if($store>0)
        {
            $store_str = " and bill_items.store_id=?";
            array_push($date, $store);
        }

        if($cashier>0)
        {
            $cashier_str = " and (last_updated_by=? or user_id=?)";
            array_push($date, $cashier);
            array_push($date, $cashier);
        }

        $sql = "select idbills,customer,company,room,sum(qty*unit_price) as bill_total,(amount_paid-change_returned) as paid from bills
                join bill_items on bill_items.bill_id=idbills
                join products on products.id = bill_items.product_id
                join categories on categories.id=products.category_id
                where deleted=0 and date(bills.date) between ? and ? and status =".\ORG\Bill::ASSIGNED." {$store_str} {$cashier_str} group by idbills";

        $data = \DB::select($sql,$date);
        return $data;
    }

    public static function CreditsSummary($date,$store=0,$cashier=0)
    {
        $store_str  = "";
        $cashier_str = "";

        if($store>0)
        {
            $store_str = " and bill_items.store_id=?";
            array_push($date, $store);
        }

        if($cashier>0)
        {
            $cashier_str = " and (last_updated_by=? or user_id=?)";
            array_push($date, $cashier);
            array_push($date, $cashier);
        }

        $sql = "select idbills,customer,room,sum(qty*unit_price) as bill_total,(amount_paid-change_returned) as paid from bills
                join bill_items on bill_items.bill_id=idbills
                join products on products.id = bill_items.product_id
                join categories on categories.id=products.category_id
                where deleted=0 and date(bills.date) between ? and ? and status =".\ORG\Bill::CREDIT." {$store_str} {$cashier_str} group by idbills";

        $data = \DB::select($sql,$date);
        return $data;
    }

    public static function SalesSummary($date,$store=0)
    {
         $store_str  = "";
         $store_join = "";
        if($store>0)
        {
            $store_str = " and bill_items.store_id=?";
            $store_join = "
            join products on products.id = bill_items.product_id
            join categories on categories.id=products.category_id ";
            array_push($date, $store);
        }

        $sql= "select idbills,company,customer,room, sum(qty*unit_price) as bill_total,bill_total as total_amount,(amount_paid-change_returned) as paid from bills
        join bill_items on bill_items.bill_id=idbills

            $store_join
            where  deleted=0  and (date(bills.date) between ? and ?) $store_str and bills.status=".\ORG\Bill::PAID." group by idbills" ;

             $data = \DB::select($sql,$date);

        if(count($date)>2){
            array_pop($date);
        }


        $pays = \DB::select("select bill_id,sum(bank_card) as bank_card,sum(cash) as cash,sum(check_amount) as check_amount from payments where void=0 and (date(date) between ? and ?) group by bill_id",$date);

        $bill_pay = array();

        foreach($pays as  $pay)
        {
            $bill_pay[$pay->bill_id] = $pay;
        }



        return ['bills'=>$data,'pays'=>$bill_pay];
    }

    public static function Credits(Array $date,$cashier=0)
    {
    	$bills = \DB::select("SELECT idbills,customer,bill_total,amount_paid,username,bills.date FROM bills
            join users on users.id =bills.user_id
            where status=".\ORG\Bill::CREDIT." and deleted=0 and (date(bills.date) between ? and ?)",$date);

        $bill_items = array();

        foreach($bills as  $bill)
        {
            $items = \DB::select("SELECT product_name,unit_price,qty from bill_items
             join products on products.id = product_id
            where bill_id=?",[$bill->idbills]);

            if(isset($bill_items[$bill->idbills]))
            {
                array_push($bill_items[$bill->idbills],$items);
            }else {
                $bill_items[$bill->idbills] = $items;
            }

        }

    	return ["bills"=>$bills,"bill_items"=>$bill_items];
    }

    public static function Sales(Array $date,$cashier=0,$store=0)
    {
        $cashier_str ="";
        $store_str = "";
        $store_join ="";
        if(isset($cashier) && $cashier >0)
        {
            $cashier_str = " and user_id=?";
            array_push($date, $cashier);
        }

        if(isset($store) && $store>0)
        {
            $store_str = " and bill_items.store_id=?";
            $store_join = " join categories on categories.id=category_id join store on store.idstore = bill_items.store_id ";
            array_push($date, $store);
        }

        $bill_items = \DB::select("select sum(unit_price*qty) as item_sum,status,idbills,customer,room,group_concat(product_name) as product,group_concat(qty) as quantity,group_concat(unit_price) as unitprice,bill_total as total,(amount_paid-change_returned)as paid from bills
            join bill_items on bill_items.bill_id = idbills
            join products on products.id = bill_items.product_id
            $store_join
             where status <> 4 and  date(bills.date) between ? and ? and bills.deleted=0 $cashier_str $store_str
            group by idbills order by idbills",$date);

        $bills = \DB::select("select sum(bill_total) as total,company,status from bill_status left join bills on bills.status=status_code where date(bills.date) between ? and ?  and status in (1,2,3,5) and deleted=0 $cashier_str group by status order by status desc",$date);

        $pays = \DB::select("select bill_id,sum(bank_card) as bank_card,sum(cash) as cash,sum(check_amount) as check_amount from payments where void=0 and (date(date) between ? and ?) $cashier_str group by bill_id",$date);

        $bill_pay = array();

        foreach($pays as  $pay)
        {
            $bill_pay[$pay->bill_id] = $pay;
        }

        return ["bills"=>$bills,"bill_items"=>$bill_items,"bill_pay"=>$bill_pay];
    }

    public static function Bills(Array $range,$store=0,$cashier=0,Array $status=[])
    {

        $params = $range;
        $where_cashier = "";
        $join_store = "";
        $where_store = "";
        $where_status = "";
        if(!is_numeric($cashier))
        {
            return;
        }
        if($cashier>0)
        {
            $where_cashier = " (bills.user_id=$cashier or bills.last_updated_by=$cashier or bills.user_id=0) and";
        }

        if(count($status)>0)
        {
            $where_status = " status in (".implode(',', $status).") and ";
        }


        $bills= \DB::select("select idbills,customer,last_updated_by,bills.user_id,bill_total,room,status_name,status,username,bills.date,status_name,waiter_name,sum(bank_card) as card,sum(cash) as cash,sum(check_amount)  as check_amount from bills left join payments on payments.bill_id=idbills and void=0 left join users on users.id = bills.user_id join waiters on waiters.idwaiter=waiter_id join bill_status on status_code=status where deleted=0 and $where_cashier $where_status date(bills.date) between ? and  ?   group by idbills order by idbills desc ",$params);

        foreach ($bills as $bill) {
           $params= [$bill->idbills];

           if($store>0)
           {
             $join_store = " join categories on categories.id=category_id join store on store.idstore = bill_items.store_id";
             $where_store = " and bill_items.store_id=?";
             array_push($params, $store);
           }

            $bill->items = \DB::select("select product_name,bill_items.qty,bill_items.unit_price from bill_items join products on products.id=product_id $join_store where bill_id=? $where_store",$params);
        }

        return $bills;
    }

    public static function Orders(Array $range,$store=0,$waiter=0,Array $status=[])
    {

        $params = $range;
        $where_waiter = "";
        $join_store = "";
        $where_store = "";

        if(!is_numeric($waiter))
        {
            return;
        }
        if($waiter>0)
        {
            $where_waiter = " (orders.waiter_id=$waiter) and";
        }

        if(count($status)>0)
        {
            $where_status = " status in (".implode(',', $status).") and ";
        }


        $bills= \DB::select("select idorders,customer,store_name,deleted,has_bill,stock,bill_id,orders.date,sum(unit_price*qty) as total,waiter_id,waiter_name,waiters.lastname
 from orders
 join waiters on idwaiter = orders.waiter_id
 join order_items on order_items.order_id = idorders
 join store on idstore = store_id
 where $where_waiter date(orders.date) between ? and  ?  group by idorders order by idorders desc ",$params);

        foreach ($bills as $bill) {
            $params= [$bill->idorders];

            if($store>0)
            {
                $join_store = " join categories on categories.id=category_id join store on store.idstore = order_items.store_id";
                $where_store = " and order_items.store_id=?";
                array_push($params, $store);
            }

            $bill->items = \DB::select("select product_name,order_items.qty,order_items.unit_price from order_items join products on products.id=product_id $join_store where order_id=? $where_store",$params);
        }

        return $bills;
    }


    public static function Products($date,$store=0)
    {
    	$store_str = "";

		if($store>0)
		{
			$store_str = " and bill_items.store_id=?";
            array_push($date,$store);
		}

		$sql = "select product_name,category_id,user_created,unit_price as unit_price,sum(qty) as qty,store_name from bill_items join bills on idbills=bill_id  join products on id=product_id left join categories on categories.id = products.category_id left join store on store.idstore = bill_items.store_id where deleted=0 and (date(bills.date) between ? and ?) $store_str group by products.id,unit_price order by bill_items.store_id";
		$data= \DB::select($sql,$date);

		$free = \DB::select("select sum(qty*unit_price) as free from bills join bill_items on bill_id=idbills where (date(date) between ? and ?) and deleted=0 and status =".\ORG\Bill::OFFTARIFF."",$date);

        $free = count($free)>0 ? $free[0]->free : 0 ;

        if($store==3){
            $free = 0;
        }

        return ["data"=>$data,"free"=>$free];

    }

    public static function Cashier($date,$cashier=0)
    {
    	$sql = "select username,sum(bank_card) as card,sum(cash) as cash from payments join users on users.id =user_id where void=0 and date(payments.date) between ? and ? group by user_id";
		$data = \DB::select($sql,$date);

		return ["data"=>$data,"credits"=>""];
    }

    public static function WaiterSales($date,$waiter=0)
    {
    	$sql = "SELECT waiters.waiter_name,waiter_id, product_name,sum(qty) as qty FROM bills
                join bill_items on bill_items.bill_id = idbills
                left join waiters on waiters.idwaiter = waiter_id
                join products on products.id = product_id
                where date(bills.date) between ? and ? and bills.status not in(?) and deleted = 0 ".($waiter > 0 ? " and waiter_id=?" : "")."
                group by waiter_id,product_id";

        $date[] = \ORG\Bill::SUSPENDED;
        if($waiter > 0){
            $date[] = $waiter;
        }
		$data = \DB::select($sql,$date);
        $waiters = [];

        foreach($data as $key=>$val)
        {
            if(!isset($waiters[$val->waiter_name]))
            {
                $waiters[$val->waiter_name] = array();
            }

                array_push($waiters[$val->waiter_name],
                    ["item_name"=>$val->product_name,"qty"=>$val->qty]);

        }

		return ["data"=>$waiters];
    }

    public static function CashierBills($date)
    {
        $cashier ="";
        if(isset($_GET['cashier']) && $_GET['cashier']>0)
        {
            $cashier = " and user_id=?";
            array_push($date, $_GET['cashier']);
        }

    	$sql = "select username,concat_ws(' ',waiters.firstname,waiters.lastname)as waiter,bills.date,idbills,room,bill_total from bills join users on user_id=users.id join waiters on idwaiter=waiter_id where deleted=0 and (date(bills.date) between ? and ?) $cashier";

    	$data = \DB::select($sql,$date);
    	return ['data'=>$data];
    }

    public static function CancelledBills($date)
    {
    	$sql = "select idbills,customer,bill_total,(amount_paid-change_returned) as paid,username,bills.date from bills join users on users.id=deleted_by where deleted=1 and date(bills.date) between ? and ?";
    	$data = \DB::select($sql,$date);
    	return ['data'=>$data];
    }

    public static function ReprintedBills($date)
    {
    	$sql ="select idbills,customer,bill_total,(amount_paid-change_returned) as paid,username,print_count,bills.date from bills join users on users.id=last_printed_by where print_count>1 and (date(bills.date) between ? and ?)";
    	$data = \DB::select($sql,$date);
    	return ['data'=>$data];
    }

    public static function CashierShift(Array $date)
    {
        $cashier ="";
        if(isset($_GET['cashier']) && $_GET['cashier']>0)
        {
            $cashier = " and user_id=?";
            array_push($date, $_GET['cashier']);
        }

    	$sql_bills = "select idbills,(amount_paid-change_returned) as paid,bill_total,user_id,username,status from bills join users on users.id=user_id where status <> ".\ORG\Bill::OFFTARIFF." and deleted=0 and date(bills.date) between ? and ?  $cashier order by status";
    	$sql_pay = "select sum(check_amount) as check_amount,sum(bank_card) as card,sum(cash) as cash,user_id from payments where void=0 and (date(date) between ? and ?) $cashier group by user_id";
    	$bills = \DB::select($sql_bills,$date);
    	$pays = \DB::select($sql_pay,$date);

    	$user_bill = array();

    	foreach ($bills as $bill) {
    		if(isset($user_bill[$bill->user_id])){array_push($user_bill[$bill->user_id] ,$bill); continue;}
    		$user_bill[$bill->user_id] = [$bill];
    	}

    	return ["data"=>$user_bill,"pays"=>$pays];
    }

    public static function StockPOSRelation()
    {
        $sql = "SELECT org_pos.products.id as posID,product_name as pos_name,org_pos.product_price.price,org_stock.products.name as stock_name,org_stock.products.id as stockID FROM org_pos.products
            left join
            org_stock.products on org_stock.products.id = org_pos.products.stock_id
            left join org_pos.product_price on   org_pos.product_price.product_id = org_pos.products.id
            where org_pos.products.category_id=1 and org_stock.products.category_id=1 and user_created=0";
        $data=   \DB::select($sql);
        return ["data"=>$data];
    }

    public static function jsonProducts()
    {
    	return \DB::select("select products.id,product_name from products where stock_id=0 and category_id=1 order by product_name asc");
    }

    public static function logs($date,$cashier=0)
    {
        $cashier_str = "";
        if($cashier>0){
            array_push($date,$cashier);
            $cashier_str = " and user_id=?";
        }
    	return \DB::select("SELECT concat(firstname,' ',lastname)as user,type,action,logs.date FROM logs join users on users.id = user_id where date(logs.date) between ? and ? {$cashier_str}",$date);
    }

    public static function daySales($date)
    {
        $sql = "SELECT store.store_name, date(date),sum(unit_price*qty) as amount FROM org_pos.bill_items
join bills on bills.idbills  = bill_id
join store on store.idstore = store_id
where deleted = 0 and status not in (1)
group by date(date),store_id where date(date) between ? and  ?";
        $data = \DB::select($sql,$date);

        return $data;
    }

    public static function turnover($date)
    {
      $amount = \DB::select("select sum(bill_total) as amount from bills where deleted=0 and status not in ('".\ORG\Bill::OFFTARIFF."','".\ORG\Bill::SUSPENDED."') and date(date) between ? and ?",$date);
      return count($amount) > 0 ? $amount[0]->amount : 0;
    }

    public static function prev_turnover($date)
    {
      $d1 = new Carbon($date[0]);
      $d2 = new Carbon($date[1]);
      $days = $d1->diff($d2)->days+1;
      $rangex  = $date;
      $rangex[1] = (new Carbon($rangex[0]))->addDays(-1)->format("Y-m-d");
      $rangex[0] = (new Carbon($rangex[0]))->addDays(-$days)->format("Y-m-d");
      $amount = \DB::select("select sum(bill_total) as amount from bills where deleted=0 and status not in ('".\ORG\Bill::OFFTARIFF."','".\ORG\Bill::SUSPENDED."') and date(date) between ? and ?",$rangex);
      return count($amount) > 0 ? $amount[0]->amount : 0;
    }

    public static function storesSales($date)
    {
      $stores_sales = \DB::select("select sum(unit_price*qty) as amount,store_name from bills join bill_items on bill_items.bill_id=idbills join products on products.id=bill_items.product_id join categories on categories.id = products.category_id join store on store.idstore = bill_items.store_id where deleted=0 and status not in ('".\ORG\Bill::OFFTARIFF."','".\ORG\Bill::SUSPENDED."') and date(bills.date) between ? and ? group by bill_items.store_id",$date);
      $stores =\DB::select("select idstore,store_name from store");
      $stores_data = ["stores"=>$stores,"sales"=>$stores_sales];
      return count($stores_data) > 0 ? $stores_data : [];

    }
}
