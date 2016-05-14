<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use \ORG;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{

    private $sql ="";
    private $data;
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($id=0)
    {
        
        $banks = DB::connection("mysql")->select("SELECT bank_name,account_code FROM bank_accounts join banks on idbanks=bank_id");
        $hotel = DB::connection("mysql")->select("SELECT hotel_name,country,city,email1,phone1,phone2,TIN,VAT,moto,logo,address_line1 FROM orgdb2.hotel");
        
        $invoice_data =  array();

        if($id > 0){

            $invoice  = DB::connection("mysql")->select("select * from invoices where idinvoices=".$id);
            $items = DB::connection("mysql")->select("select * from invoice_items where invoice_id=".$id);

            $invoice_data =array($invoice,$items);
        }

        return \View::make("/ORGFrontdesk/Invoice/NewInvoice")->with(["hotelInfo"=>$hotel,"banks"=>$banks,"invoice_data"=>$invoice_data]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $req)
    {
        $this->data = $req->all();

        $db_vals = array();
        $q = "";
        foreach ($this->data as $key => $value) {
            if($value ==""){continue;}
            

            if(Str::startsWith($key,"qty")){
                $q ="(:inv,'".$value."',";
            }else {
                $q .="'".$value."',";
            }

            if(Str::startsWith($key,"itemtotal")){
                $q = trim($q,',');
                $q .=")";
             array_push($db_vals, $q);

            }

        }

        $items = implode(',', $db_vals);
       

        $this->sql = "insert into invoice_items (invoice_id,quantity,description,uprice,row_total) values ".$items;

        

        $res = DB::connection("mysql")->transaction(function () {

            
            $id = DB::connection("mysql")->table("invoices")->insertGetId(
                [
                    "date"=>\ORG\Dates::WORKINGDATE(true),
                    "company_name"=>$this->data['company_name'],
                    "city"=>$this->data['city'],
                    "phone"=>$this->data['phone'],
                    "country"=> $this->data['country'],
                    "address_line"=>$this->data['address1'],
                    "user_id"=>$_GET['user'],
                    "invoice_type"=>$this->data['invoice_type'],
                    "sub_total"=>str_replace(',', '', $this->data['subtotal']),
                    "total" =>str_replace(',', '', $this->data['total']),
                    "tax"=>$this->data['tax']
                ]
            );

             DB::connection("mysql")->insert(str_replace(':inv', $id, $this->sql));

             return $id;

         
        });

        return "$res";
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return $this->create($id);
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
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
