<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockReport extends Model
{
    public static function StockQuantity($id)
    {
        $condition = "";
        $params = array();

        if($id>0)
        {
            $condition = "where warehouses.id=?";
            $params = [$id];
        }

        $data = \DB::connection("mysql_stock")->select("SELECT warehouses.name as stock, products.code,products.name,warehouses_products.quantity FROM warehouses_products
        join products on products.id = warehouses_products.product_id
        join warehouses on warehouses.id = warehouse_id $condition",$params);

        return ["data"=>$data];
    }

    public static function DamagedProducts($date)
    {
        $data = \DB::connection("mysql_stock")->select("SELECT products.name as product_name,damage_products.quantity,warehouses.name as stock,date FROM damage_products
        join products on products.id = damage_products.product_id
        join warehouses on warehouses.id = damage_products.warehouse_id where date(date) between ? and ?",$date);
        return ["data"=>$data];
    }

    public static function Purchases($date,$stock=0)
    {
        if(!is_numeric($stock))
        {
            $stock = 0;
        }

        $sql = "SELECT id,reference_no,supplier_name,user,total,note,date FROM purchases where date(date) between ? and ?";

        if($stock > 0){
            $data = \DB::connection("mysql_stock")->select($sql." and warehouse_id=$stock",$date);
            return ["data"=>$data,"selectedWarehouse"=>self::getWarehouse($stock)];
        }else {
            $data = \DB::connection("mysql_stock")->select($sql,$date);
            return ["data"=>$data];
        }

    }

    public static function PurchaseItems($id)
    {
        $data = \DB::connection("mysql_stock")->select("SELECT reference_no,note,supplier_name,product_code,product_name,unit_price,quantity,gross_total FROM purchase_items join purchases on purchases.id=purchase_id where purchase_id=?",[$id]);
        return ["data"=>$data];
    }

    public static function Sales($date,$stock=0)
    {
        if(!is_numeric($stock))
        {
            $stock = 0;
        }

        $sql = "SELECT id,reference_no,biller_name,user,total,note,date FROM sales where date(date) between ? and ?";

        if($stock > 0){
            $data = \DB::connection("mysql_stock")->select($sql." and warehouse_id=$stock",$date);
            return ["data"=>$data,"selectedWarehouse"=>self::getWarehouse($stock)];
        }else {
            $data = \DB::connection("mysql_stock")->select($sql,$date);
            return ["data"=>$data];
        }
    }

    public static function Requisitions($date)
    {
        $sql = "SELECT concat_ws(' ',first_name,last_name) as user,idrequisition,sum(sub_total) as total ,note,requisition.date FROM requisition
        join requisition_items on requisition.idrequisition = requisition_id
        join users on users.id = user_id where date(requisition.date) between ? and ? group by idrequisition";

        $data = \DB::connection("mysql_stock")->select($sql,$date);
        return ["data"=>$data];
    }

    public static function Transfers($date)
    {
        $sql = "select id,transfer_no,from_warehouse_name,to_warehouse_name,note,user,tr_total,date from transfers where date between ? and ?";
        $data = \DB::connection("mysql_stock")->select($sql,$date);
        return ["data"=>$data];
    }

    public static function SaleItems($id)
    {
        $data = \DB::connection("mysql_stock")->select("SELECT reference_no,note,biller_name,product_code,product_name,unit_price,quantity,gross_total FROM sale_items join sales on sales.id=sale_id where sale_id=?",[$id]);
        return ["data"=>$data];
    }

    public static function getWarehouses()
    {
        $data = \DB::connection("mysql_stock")->select("select id,name from warehouses");
        return $data;
    }

    public static function getWarehouse($id)
    {
        $data = \DB::connection("mysql_stock")->select("select id,name from warehouses where id=?",[$id])[0];
        return $data;
    }

    public static function StockOverview($range,$stock=0,$id=0)
    {
        $start_date = $range[0];
        $end_date = $range[1];

        #region sql1
        $sql = "select products.name,unit,quantity,
        (select concat(COALESCE(sum(purchase_items.quantity),0),'#',COALESCE(sum(purchase_items.gross_total),0))  from purchases
        join purchase_items on purchase_items.purchase_id = purchases.id
        where product_id = products.id and purchases.date between '$start_date' and '$end_date') as stockin,

        (select concat(COALESCE(sum(sale_items.quantity),0),'#',COALESCE(sum(sale_items.gross_total),0))  from sales
        join sale_items on sale_items.sale_id = sales.id
        where product_id = products.id and sales.date between '$start_date' and '$end_date') as stockout,

        (select COALESCE(sum(damage_products.quantity),0)  from damage_products
        where product_id = products.id and date between '$start_date' and '$end_date') as  damaged,

        @stin:=(select COALESCE(sum(purchase_items.quantity),0)  from purchases
        join purchase_items on purchase_items.purchase_id = purchases.id
        where product_id = products.id and purchases.date >= '$start_date') as stin,

        @stout:=(select COALESCE(sum(sale_items.quantity),0)  from sales
        join sale_items on sale_items.sale_id = sales.id
        where product_id = products.id and sales.date >= '$start_date') as stout,

        @damagedp:=(select COALESCE(sum(damage_products.quantity),0)  from damage_products
                where product_id = products.id and date >= '$start_date') as damagedp,

        (quantity+@damagedp-@stin+@stout) as opening

        from products

        " .($id>0 ? " where products.id=".$id : "");

        #endregion

        #region SQL2
        $sql2 = "select products.name,unit,warehouses_products.quantity,warehouses.name as warehouse,
        (select concat(COALESCE(sum(purchase_items.quantity),0),'#',COALESCE(sum(purchase_items.gross_total),0))  from purchases
        join purchase_items on purchase_items.purchase_id = purchases.id
        where product_id = products.id and purchases.warehouse_id=$stock and purchases.date between '$start_date' and '$end_date') as stockin,

        (select concat(COALESCE(sum(sale_items.quantity),0),'#',COALESCE(sum(sale_items.gross_total),0))  from sales
        join sale_items on sale_items.sale_id = sales.id
        where sales.warehouse_id=$stock and product_id = products.id and sales.date between '$start_date' and '$end_date') as stockout,

         (select COALESCE(sum(damage_products.quantity),0)  from damage_products
        where product_id = products.id and warehouse_id=$stock and  date between '$start_date' and '$end_date') as  damaged,

        (select COALESCE(sum(transfer_items.quantity),0)  from transfers
        join transfer_items on transfer_items.transfer_id = transfers.id
        where to_warehouse_id=$stock and product_id = products.id and transfers.date between '$start_date' and '$end_date' ) as trin,

        (select COALESCE(sum(transfer_items.quantity),0)  from transfers
        join transfer_items on transfer_items.transfer_id = transfers.id
        where from_warehouse_id=$stock and product_id = products.id and transfers.date between '$start_date' and '$end_date') as trout,

        @stin:=(select COALESCE(sum(purchase_items.quantity),0)  from purchases
        join purchase_items on purchase_items.purchase_id = purchases.id
        where purchases.warehouse_id=$stock and product_id = products.id and purchases.date >= '$start_date') as stin,

        @stout:=(select COALESCE(sum(sale_items.quantity),0)  from sales
        join sale_items on sale_items.sale_id = sales.id
        where sales.warehouse_id=$stock and product_id = products.id and sales.date >= '$start_date') as stout,

        @trsin:=(select COALESCE(sum(transfer_items.quantity),0)  from transfers
        join transfer_items on transfer_items.transfer_id = transfers.id
        where to_warehouse_id=$stock and product_id = products.id and transfers.date >= '$start_date') as transferin,

        @trsout:=(select COALESCE(sum(transfer_items.quantity),0)  from transfers
        join transfer_items on transfer_items.transfer_id = transfers.id
        where from_warehouse_id=$stock and product_id = products.id and transfers.date >= '$start_date') as transferout,

        @damagedp:=(select COALESCE(sum(damage_products.quantity),0) from damage_products
                where product_id = products.id and warehouse_id=$stock and date >= '$start_date') as damagedp,

        (warehouses_products.quantity+@damagedp-@stin+@stout-@trsin+@trsout) as opening

        from products
        join warehouses_products on warehouses_products.product_id = products.id join warehouses on warehouses.id = warehouses_products.warehouse_id where warehouses.id=$stock ".($id>0 ? " and products.id=".$id : "");
        #endregion

        if($stock > 0){
            $data = \DB::connection("mysql_stock")->select(\DB::raw($sql2));
            return ["data"=>$data,"selectedWarehouse"=>self::getWarehouse($stock)];
        }else {
            $data = \DB::connection("mysql_stock")->select(\DB::raw($sql));
            return ["data"=>$data];
        }


    }
}
