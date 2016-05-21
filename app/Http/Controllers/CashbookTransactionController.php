<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CashbookTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($id)
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $cashbooks = \DB::connection("mysql_backoffice")->select("select cashbookid,cashbook_name from cash_book");
        return \View::make("Backoffice.addTransaction",["cashbooks"=>$cashbooks]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        \DB::beginTransaction();

        try {
            $sign = $request->input("type")=="IN" ? "+" : "-";
            $date = $request->input("date",\ORG\Dates::$RESTODATE);
            $real_date = \ORG\Dates::$RESTODT;
            $prev_balance = $request->input("prev_balance",0);

            $trans = \DB::connection("mysql_backoffice")->select("(select new_balance as amt from cashbook_transactions where cashbook_id=? and deleted=0 and date(date)<=? order by transactionid desc limit 1)",[$request->input("cashbook"),$date]);


            $amt = ($trans) ? $trans[0]->amt : 0;

            if($sign=="+")
            {
                $amt += $request->input("amount",0);
            }else {
                $amt -= $request->input("amount",0);
            }

            $date_dt = $date ;
            $real_date_dt= explode(" ",$real_date)[0];

            $in_past = strtotime($date_dt) < strtotime($real_date_dt);

           

            if($in_past)
            {
                //update last one  after this new one
                //$up2 = \DB::connection("mysql_backoffice")->update("update cashbook_transactions set new_balance=new_balance$sign? where date(date) between ? and ? and cashbook_id=?",[$request->input("amount"),$date,$real_date_dt,$request->input("cashbook")]);
            }else {
                //Normaly
                if($sign=="+"){
                    $amt = $prev_balance + $request->input("amount",0);
                }else{
                    $amt = $prev_balance - $request->input("amount",0);
                }
            }

            \DB::connection("mysql_backoffice")->insert("insert into cashbook_transactions (type,amount,user_id,motif,cashbook_id,date,add_date,new_balance) values (?,?,?,?,?,?,?,?)",[ $request->input("type"), $request->input("amount"),\Auth::user()->id, $request->input("motif"),$request->input("cashbook"),$date." ".date("H:i:s"),$real_date,$amt]);

            $up = \DB::connection("mysql_backoffice")->update("update cash_book set balance=balance$sign? where cashbookid=?",[$request->input("amount"), $request->input("cashbook")]);

            if($up>-1){
                \DB::commit();
                return \Redirect::back()->with("date",$date);
            }else {
                \DB::rollBack();
                return \Redirect::back()->action('BackofficeController@index')->with("error","Error saving transaction, Please try again");
            }

        }
        catch(Exception $e)
        {
            \DB::rollBack();
        }
    }



    public function show($id)
    {
        if(\Auth::user()->level < 8)
        {
            return;
        }
        $sign = $_GET['type']=="IN" ? "-":"+";
        \DB::beginTransaction();

        $up1 = \DB::connection("mysql_backoffice")->update("update cashbook_transactions set deleted=1 where transactionid=?",[$id]);

        $up2 = \DB::connection("mysql_backoffice")->update("update cash_book set balance=balance$sign? where cashbookid=?",[$_GET['amount'],$_GET['cashbook']]);

        if($up1>0 && $up2>0)
        {
            \DB::commit();
            return 1;
        }else {
            \DB::rollBack();
            return 0;
        }

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
