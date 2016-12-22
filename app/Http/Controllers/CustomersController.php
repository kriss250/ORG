<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CustomersController extends Controller
{
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
    public function create()
    {
        return \View::make("Pos.NewCustomer");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $in =0;
        $favorite = isset($data['favorite']) ? "1" : "0";

         $vl =  \Validator::make($data, [
            'nickname' => 'required|max:255|unique:customers'
        ]);

         if(count($vl->errors()) == 0)
         {

             $in=  \DB::table("customers")->insert([
                 "firstname" =>$data['firstname'],
                 "lastname" => $data['lastname'],
                 "company"=>$data['company'],
                 "email"=>$data['email'],
                 "phone"=>$data['phone'],
                 "address"=>$data['address'],
                 "nickname"=>$data['nickname'],
                 "favorite"=>$favorite
                 ]);
         }

         if($in>0){
            return json_encode(["errors"=> null,'success'=>1]);
       }else {
            return json_encode(["errors"=>$vl->errors(),"success"=>0]);
       }
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
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
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


    public function billFinder()
    {
        $bills= null;
        if(isset($_GET['find']))
        {
            //All Names
            if(strlen($_GET['firstname']) > 0 && strlen($_GET['lastname']) > 0)
            {
                $bills = \Kris\Frontdesk\Reservation::whereIn("status",[\Kris\Frontdesk\Reservation::CHECKEDOUT,\Kris\Frontdesk\Reservation::CHECKEDIN])
                ->join("guest","guest_id","=","id_guest")
                ->whereBetween("checkin",[$_GET['startdate'],$_GET['enddate']])
                ->where("firstname","like","%".$_GET['firstname'])
                ->where("lastname","like","%".$_GET['lastname'])
                ->orderBy("idreservation","desc")
                ->limit("150")->get();
            }else{
                $bills = \Kris\Frontdesk\Reservation::whereIn("status",[\Kris\Frontdesk\Reservation::CHECKEDOUT,\Kris\Frontdesk\Reservation::CHECKEDIN])->orderBy("idreservation","desc")->limit("20")->get();

             }


            //Fristname
            if(strlen($_GET['firstname'])>0  && strlen($_GET['lastname'])==0 )
            {
                $bills = \Kris\Frontdesk\Reservation::whereIn("status",[\Kris\Frontdesk\Reservation::CHECKEDOUT,\Kris\Frontdesk\Reservation::CHECKEDIN])
                ->join("guest","guest_id","=","id_guest")
                ->whereBetween("checkin",[$_GET['startdate'],$_GET['enddate']])
                ->where("firstname","like","%".$_GET['firstname'])
                ->orderBy("idreservation","desc")
                ->limit("150")->get();
            }

            //Lastname
            if(strlen($_GET['lastname'])>0  && strlen($_GET['firstname'])==0 )
            {
                $bills = \Kris\Frontdesk\Reservation::whereIn("status",[\Kris\Frontdesk\Reservation::CHECKEDOUT,\Kris\Frontdesk\Reservation::CHECKEDIN])
                ->join("guest","guest_id","=","id_guest")
                ->whereBetween("checkin",[$_GET['startdate'],$_GET['enddate']])
                ->where("lastname","like","%".$_GET['lastname'])
                ->orderBy("idreservation","desc")
                ->limit("150")->get();
            }


        }else {
            $bills = \Kris\Frontdesk\Reservation::whereIn("status",[\Kris\Frontdesk\Reservation::CHECKEDOUT,\Kris\Frontdesk\Reservation::CHECKEDIN])->orderBy("idreservation","desc")->limit("20")->get();
        }

        return \View::make("Backoffice.BillFinder",["customer"=>"","bills"=>$bills]);
    }

    public function printBill($id)
    {
        return \View::make("Backoffice.FoBillView",["id"=>$id]);
    }
}
