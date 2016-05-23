<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use \DB;
use \Datatables;
use ORG;
class ProductsController extends Controller
{
    private $data;
    private $saved = false;
    /**
     * Display a listing of the resource.
     * Product list - (Page)
     * @return Response
     */
    public function index()
    {

        $join = "join categories on category_id = categories.id
                        left join sub_categories on sub_categories.id = subcategory_id
                        join product_price on price_id = product_price.id";


        if(isset($_GET['json'])){

            $columns = array(
                array( 'db' => 'products.id', 'dt' => 0 ),
                array( 'db' => 'products.product_name', 'dt' => 1 ),
                array( 'db' => 'category_name',  'dt' => 2 ),
                array( 'db' => 'sub_category_name',   'dt' => 3 ),
                array( 'db' => 'price',     'dt' => 4 ),
                array( 'db' => 'products.description','dt' => 5),
                array( 'db' => 'products.date','dt' => 6,
                        'formatter'=>function($d,$row){
                            return date(ORG\Dates::DSPDATEFORMAT,strtotime($d));
                        }
                    )
            );

            return Datatables\SSP::simple( $_GET, "products", "products.id", $columns,$join );


        }

        return \View::make("/Pos/ProductList");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $cats = DB::select("SELECT id,category_name FROM categories");

        if(isset($_GET['id'])){
            $prod = DB::select("SELECT product_name,products.description,category_name,sub_category_name,price,tax,products.category_id,sub_categories.id as sub_cat FROM products
                join categories on products.category_id = categories.id join product_price  on product_price.id = price_id
                left join sub_categories on subcategory_id = sub_categories.id where products.id=?",[$_GET['id']])[0];

            return \View::make("/Pos/NewProduct",["cats"=>$cats,"prod"=>$prod]);
        }

        return \View::make("/Pos/NewProduct",["cats"=>$cats]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $req)
    {

        $errors  = array();
        $message = "";
        $this->data = $req->all();

        $validator = \Validator::make($req->all(), [
            'product_name' => 'required|min:2',
            'product_price'=>'required|numeric'
        ]);

        if($this->data['category']==0){
            array_push($errors, "Please Choose Category");
        }

        if ($validator->fails()) {
            array_push($errors, $validator->errors());
        }

        if(count($errors)==0){
            \DB::transaction(function(){


                $product_id = DB::table('products')->insertGetId([
                    'category_id' => $this->data["category"],
                    "subcategory_id" => isset($this->data['sub_category']) ? $this->data['sub_category'] : 0 ,
                    "price_id"=>0,
                    "product_name"=> $this->data['product_name'],
                    "description"=>$this->data['description'],
                    "date"=>date(\ORG\Dates::DBDATEFORMAT)
                    ]
                );

                 $price_id = DB::table('product_price')->insertGetId(
                    ['product_id' =>$product_id, "price" => $this->data['product_price'],"tax"=>$this->data["product_tax"],"date"=>date(\ORG\Dates::DBDATEFORMAT)]
                );

                 $this->saved = DB::table("products")->where('id',$product_id)->update(["price_id"=>$price_id]);

            });
        }

        if($this->saved){
            $message = "Product Saved";
        }else {
            array_push($errors, "Error Saving Product");
        }

        $response = array(
            'message' => $message,
            'errors'=> $errors
        );

        return json_encode($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id,Request $req)
    {
        $res = new \stdClass();
        $res->errors = array();
        $res->message = "";

        $data = $req->all();


        try {
            DB::transaction(function() use($id,$data){
                $sub_catx = isset($data['sub_category']) ? $data['sub_category'] : "0" ;

                 DB::update("update products set product_name=?,category_id=?,subcategory_id=?,description=? where id=?",
                    [$data['product_name'],$data['category'],$sub_catx,$data['description'],$id]
                );

                 if(isset($data['prev_price']) && $data['prev_price'] !=$data['product_price'] ){

                     $price_id = DB::table("product_price")->insertGetId([
                        "product_id"=>$id,
                        "price"=>$data['product_price'],
                        "tax"=>$data['product_tax'],
                        "date"=>\ORG\Dates::$RESTODT
                    ]);

                    DB::table("products")->where('id',$id)->update([
                        "price_id"=>$price_id
                    ]);
                }
            });

            $res->message = "Product Updated";
        }
        catch(Exception $ex){
            array_push($res->errors, "Unable to update the product, Please try again later");
        }

        return json_encode($res);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $update = \DB::update("update products set active=0 where id=?",[$id]);
        return "$update";
    }
    //Product list POS Home page
    public function jsonReq()
    {

        $id = 0;

        if(isset($_GET['category'])){
            $id = $_GET['category'];
        }
        $favo = (isset($_GET['favorite']) && $_GET['favorite'] == "1") ? " and favorite=1":"";

        $sql = "SELECT products.id,product_name,category_name,price,stock_id FROM products
        join categories on categories.id = category_id
        join product_price on price_id = product_price.id ".((isset($_GET['favorite']) && $_GET['favorite'] == "1") ? " where favorite=1":"")." order by product_name asc limit 70";

        if(isset($_GET['store']) && $_GET['store']>0 ){
            $sql2 = "SELECT products.id,product_name,category_name,price,stock_id FROM products
            join categories on categories.id = category_id
            join store on idstore = store_id
            join product_price on price_id = product_price.id where idstore=? $favo order by product_name asc limit 70";

            return json_encode(DB::select($sql2,[$_GET['store']]));
        }

        if($id>0){
            $sql = "SELECT products.id,product_name,category_name,price,stock_id FROM products
            join categories on categories.id = category_id
            join product_price on price_id = product_price.id where category_id=? $favo order by product_name asc limit 70";

            return json_encode(DB::select($sql,[$id]));
        }

        //Default product list
        return json_encode(DB::select($sql));
    }

    public function searchProduct()
    {
        $q =  "%".$_GET['q']."%";

        return json_encode(DB::select("SELECT products.id,product_name,category_name,price,stock_id FROM products
        join categories on categories.id = category_id
        join product_price on price_id = product_price.id where product_name LIKE ? and user_created=0  order by favorite desc  limit 30",[$q]));
    }

    public function CreateCustomProduct(Request $req)
    {
        $prod_id = 0;
        $data = $req->all();

        DB::transaction(function() use (&$data,&$prod_id){

            $id = DB::table("products")->insertGetId([
                "product_name"=>$data['product_name'],
                "description"=>"Custom Product by:".\Auth::user()->username,
                "category_id"=>$data['category'],
                "subcategory_id"=>0,
                "price_id"=>0,
                "user_created"=>"1",
                "date"=> \ORG\Dates::$RESTODT
            ]);



            $price= DB::table("product_price")->insertGetId([
                "product_id"=>$id,

                "price"=>$data['product_price'],
                "tax"=>"18",
                "date"=> \ORG\Dates::$RESTODT
            ]);

            DB::table("products")->where('id',$id)->update([
                "price_id"=>$price
            ]);

            $prod_id = $id;
        });

        return "$prod_id";

    }

    public function markAsFavorite($prod,$state)
    {
        $updated  = DB::update("update products set favorite=? where id=?",[$state,$prod]);
        return json_encode(array($updated));
    }

    public static function removeProductsFromStock()
    {
        $warehouse_id = 11;
        \DB::beginTransaction();

        try {

            $sql = "SELECT product_id,sum(qty) as qty,stock_id,product_name,unit_price,sum(qty*unit_price) as total FROM bills
         join bill_items on bill_items.bill_id=idbills
         join products on products.id = product_id
         where stock_id > 0 and date(bills.date)=? and status<>? and deleted=0 group by product_id";


            $items = \DB::select($sql,[\ORG\Dates::$RESTODATE,\ORG\Bill::SUSPENDED]);

            $total = \DB::select("SELECT sum(qty*unit_price) as total FROM bills
         join bill_items on bill_items.bill_id=idbills
         join products on products.id = product_id
         where stock_id > 0 and date(bills.date)=?  and status<>? and deleted=0",[\ORG\Dates::$RESTODATE,\ORG\Bill::SUSPENDED])[0]->total;
            
            $sale_id = \DB::connection("mysql_stock")->table("sales")->insertGetId([
                    "warehouse_id"=> $warehouse_id,
                    "biller_id"=>"1",
                    "reference_no"=>"POS",
                    "biller_name"=>"POS",
                    "customer_id"=>"0",
                    "customer_name"=>"POS",
                    "date"=>\ORG\Dates::$RESTODATE,
                    "note"=>"POS Sales",
                    "inv_total"=>$total,
                    "total"=>$total,
                    "user"=>"POS System",
                    "pos"=>"1"
                ]);

            $sale_items = 0;
            $prod_qty = 0;
            $wa_qty = 0;

            if(count($items) > 0)
            {
                foreach($items as $item)
                {

                    $sale_items = \DB::connection("mysql_stock")->table("sale_items")->insert([
                        "sale_id"=>$sale_id,
                        "product_id"=>$item->stock_id,
                        "product_code"=> $item->product_id,
                        "product_name"=>$item->product_name,
                        "quantity"=>$item->qty,
                        "unit_price"=>$item->unit_price,
                        "gross_total"=> $item->qty * $item->unit_price
                   ]);

                    $prod_qty = \DB::connection("mysql_stock")->update("update products set quantity=quantity-? where products.id=?",[$item->qty,$item->stock_id]);
                    $wa_qty = \DB::connection("mysql_stock")->update("update warehouses_products set quantity=quantity-? where warehouse_id=? and product_id=?",[$item->qty,$warehouse_id,$item->stock_id]);
                }
            }

            if($sale_id>0 && $items > 0 && $sale_items > 0 && $prod_qty>0 && $wa_qty > 0)
            {
                \DB::commit();
            }
        }
        catch(\Exception $ex)
        {
            \DB::rollBack();
        }

    }
}

