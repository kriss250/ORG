<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class POSCreditController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
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
    
    public function export()
    {
        $internal=0;
        $id = $_GET['id'];
        
        $external = 0;
        $internal =strtolower($_GET['destination'])=="internal" ? 1 : 0;
        $external = strtolower($_GET['destination'])=="external" ? 1 : 0;
        
        if(isset($_GET['posdebt']) && $_GET['posdebt']==1)
        {
            \DB::beginTransaction();
            $q1 = \DB::update("update bills set export_credit=1 where idbills=? and status=?",[$id,\ORG\Bill::CREDIT]);
            $q2 = \DB::connection("mysql_backoffice")->insert("insert into exported_debts (bill_id,internal,external,user_id,date) values
             (?,?,?,'".\Auth::user()->id."','".date("Y-m-d")."')
            ",[$id,$internal,$external]);
             
            if($q1>0 && $q2>0)
            {
                \DB::commit(); 
                return 1;
            }else {
                 \DB::rollBack();
                 return 0;
            }
            
        }else {
            return \DB::connection("mysql_backoffice")->update("update exported_debts set external=?,internal=? where bill_id=?",[$external,$internal,$id]);
        }
    }

    public function internalDebts()
    {
        $start_date =isset($_GET['startdate']) ? $_GET['startdate'] :  date("Y-m-d",strtotime(\ORG\Dates::$RESTODT));
        $end_date = isset($_GET['enddate']) ? $_GET['enddate'] : $start_date;
        
        $range = [$start_date,$end_date];
        array_push($range,\ORG\Bill::CREDIT);
        
        $data = \DB::select("select idbills,bill_total,customer,amount_paid,bills.date,username from bills join org_backoffice.exported_debts on org_backoffice.exported_debts.bill_id=idbills join users on users.id=bills.user_id where deleted=0 and export_credit=1 and internal=1 and date(bills.date) between ? and ? and (status =?)",$range);
        return \View::make("Backoffice.InternalCredit",["data"=>$data]);
    }

    public function externalDebts()
    {
        $start_date =isset($_GET['startdate']) ? $_GET['startdate'] :  date("Y-m-d",strtotime(\ORG\Dates::$RESTODT));
        $end_date = isset($_GET['enddate']) ? $_GET['enddate'] : $start_date;
        
            $range = [$start_date,$end_date];
    
        array_push($range,\ORG\Bill::CREDIT);
        
        $data = \DB::select("select idbills,bill_total,customer,amount_paid,bills.date,username from bills join org_backoffice.exported_debts on org_backoffice.exported_debts.bill_id=idbills join users on users.id=bills.user_id where deleted=0 and export_credit=1 and external=1 and date(bills.date) between ? and ? and (status =?)",$range);
        return \View::make("Backoffice.ExternalCredit",["data"=>$data]);
    }

    public function unexportedDebts()
    {
        $start_date =isset($_GET['startdate']) ? $_GET['startdate'] :  date("Y-m-d",strtotime(\ORG\Dates::$RESTODT));
        $end_date = isset($_GET['enddate']) ? $_GET['enddate'] : $start_date;
        
        $range = [$start_date,$end_date];

        array_push($range,\ORG\Bill::CREDIT);
        
        $data = \DB::select("select idbills,bill_total,customer,amount_paid,bills.date,username from bills join users on users.id=user_id where deleted=0 and export_credit=0 and date(bills.date) between ? and ? and (status =?)",$range);
        return \View::make("Backoffice.CreditView",["data"=>$data]);
    }


}
