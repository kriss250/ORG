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

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return \View::make("Backoffice.InvoiceList");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($id=0)
    {
        return \View::make("Backoffice.CreateInvoice");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $req)
    {
        $data = \Request::all();

        $invoice = \App\Invoice::create([
            "user_id"=> \Auth::user()->id,
            "due_date"=>$data['due_date'],
            "institution"=>$data['company'],
            "address"=>$data['address'],
            "description"=>$data['description']
            ]);

        unset($data['due_date']);
        unset($data['_token']);
        unset($data['company']);
        unset($data['address']);
        unset($data['description']);

        $i= 1;

        $rows = count($data)/4;

        for($i=1;$i<=$rows;$i++)
        {
            if(!isset($data["desc_{$i}"]))
            {
                break;
            }


            if(strlen($data["desc_{$i}"]) < 1 && strlen($data["price_{$i}"]) < 1)
            {
               continue;
            }else {

                $item = array(
                "description"=>htmlentities(nl2br($data["desc_{$i}"])),
                "unit_price"=>$data["price_{$i}"],
                "qty"=>$data["qty_{$i}"],
                "date"=>$data["date_{$i}"]
                );

                $invoice->items()->create($item);
            }
           ;
        }

      return redirect()->back()->with("msg","Invoice saved successfuly");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $hotel = \DB::connection("mysql_book")->table("hotel")->first() ;
        $invoice = \App\Invoice::find($id);
        return \View::make("Backoffice.viewInvoice",["invoice"=>$invoice,"hotel"=>$hotel]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $invoice = \App\Invoice::find($id);
        return \View::make("Backoffice.EditInvoice",["invoice"=>$invoice]);
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

    }

    public function delete($id)
    {
      \App\Invoice::find($id)->delete();
      return redirect()->back();
    }


    public function showPayments($id)
    {
      $invoice = \App\Invoice::find($id);
      return \View::make("Backoffice.InvoicePayments",["invoice"=>$invoice]);
    }
}
