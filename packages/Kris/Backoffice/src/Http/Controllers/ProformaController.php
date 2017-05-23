<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use App\ProformaItem;
use \ORG;
use Illuminate\Support\Str;

class ProformaController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return \View::make("Backoffice.ProformaList");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($id=0)
    {
        return \View::make("Backoffice.CreateProforma");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $req)
    {
        $data = \Request::all();

        if($this->proformaCodeExist($data['code']))
        {
            redirect()->back()->withErrors(["Proforma Code Already Exist !"]);
        }

        $debtor = \App\Debtor::firstOrCreate([
          "name"=>trim($data['company'])
        ]);

        //echo $debtor;
        //return;
        $proforma = \App\Proforma::create([
            "user_id"=> \Auth::user()->id,
            "due_date"=>$data['due_date'],
            "institution"=>$data['company'],
            "address"=>$data['address'],
            "code"=>$data['code'],
            "description"=>$data['description'],
            "debtor_id"=>$debtor->iddebtors
            ]);

        unset($data['due_date']);
        unset($data['_token']);
        unset($data['company']);
        unset($data['code']);
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
                "date"=>$data["date_{$i}"],
                "days"=>$data["days_{$i}"]
                );

                $proforma->items()->create($item);
            }
           ;
        }

      return redirect()->back()->with("msg","Proforma saved successfuly");
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
        $proforma = \App\Proforma::find($id);
        return \View::make("Backoffice.viewProforma",["proforma"=>$proforma]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $proforma = \App\Proforma::find($id);
        return \View::make("Backoffice.EditProforma",["proforma"=>$proforma]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $data = \Request::all();
        $proforma  = \App\Proforma::find($id);
        $proforma->institution = $data['company'];
        $proforma->due_date = $data['due_date'];
        $proforma->address = $data['address'];
        $proforma->description = $data['description'];
        $proforma->code = $data['code'];
      

        //if($this->proformaCodeExist($data['code']))
        //{
        //    return redirect()->back()->withErrors(["Proforma Code not available"]);
        //}
        $proforma->save();

        \App\ProformaItem::where("proforma_id",$proforma->idproforma)->delete();

        unset($data['due_date']);
        unset($data['_token']);
        unset($data['company']);
        unset($data['address']);
        unset($data['description']);
        unset($data['code']);
        unset($data['delivery_date']);

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
                "date"=>$data["date_{$i}"],
                "days"=>$data["days_{$i}"]
                );

                $proforma->items()->create($item);
            }

        }

        return redirect()->back()->with("msg","Proforma saved successfuly");
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
      if(\Auth::user()->level > 9) \App\Proforma::find($id)->delete();
      return redirect()->back();
    }


    public function proformaCodeExist($code)
    {
        $inv = \App\Proforma::where("code","=",$code)->where(\DB::raw("year(created_at)"),"=",date("Y"))->first();
        return $inv!=null;
    }
}
