<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
    	$date = [\ORG\Dates::$RESTODATE,\ORG\Dates::$RESTODATE];
        // List Paid bills : Room posts , Credits, Paid(card,cash)
        if(isset($_GET['startdate']) && isset($_GET['enddate']))
        {
            $date= [$_GET['startdate'],$_GET['enddate']];
        }
        //cash,card
        $sql = "select idpayments,cash,bank_card,check_amount,customer,comment,payments.date,bill_id,username from payments join users on users.id = user_id join bills on bills.idbills = bill_id where void=0 and date(payments.date) between ? and ?";
        $sql2 = "select idbills,room,bill_total,bills.date,status,room,username from bills join users on users.id=user_id where deleted=0 and status in (".\ORG\Bill::ASSIGNED.") and date(bills.date) between ? and ?";

        $pays = \DB::select($sql,$date);
        $bills= \DB::select($sql2,$date);

        return \View::make("Backoffice.payments",["pays"=>$pays,"bills"=>$bills]);

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

    public function cancelBillPayments($id)
    {
           
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $pay_id= \Request::input("id");
        $bill_id = \Request::input("bill_id");

        $amount = \DB::select("select (bank_card+cash+check_amount) as amount from payments where idpayments=?",[$pay_id])[0]->amount;
        $total_amount_paid =\DB::select("select amount_paid,change_returned,bill_total from bills where idbills=?",[$bill_id])[0];
        
        \DB::beginTransaction();
        $up  =\DB::update("update payments set void=1 where idpayments=?",[$pay_id]);
        $up = \DB::update("update bills set amount_paid=amount_paid-? where idbills=?",[$amount,$bill_id]);

        if((($total_amount_paid->amount_paid-$total_amount_paid->change_returned)-$amount)==0)
        {
        	//Set status to suspended 
        	$up= \DB::update("update bills set status=? where idbills=?",[\ORG\Bill::SUSPENDED,$bill_id]);
        	\DB::commit();
        }else if(($total_amount_paid->amount_paid-$total_amount_paid->change_returned-$amount) < $total_amount_paid->bill_total)
        {
        	//Set status to credit
        	$up= \DB::update("update bills set status=? where idbills=?",[\ORG\Bill::CREDIT,$bill_id]);
        	\DB::commit();
        }  

        
        if($up>0)
        {
        	return 1;
        }else {
        	\DB::rollBack();
        	return $up;
        }
        
    }
}
