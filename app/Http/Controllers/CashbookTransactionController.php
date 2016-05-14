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
            $trans = \DB::connection("mysql_backoffice")->select("(select new_balance$sign? as amt from cashbook_transactions where cashbook_id=? and deleted=0 and date(date)=? order by transactionid desc limit 1)",[$request->input("amount"),$request->input("cashbook"),$date]);

            $amt = ($trans) ? $trans[0]->amt : 0;

            \DB::connection("mysql_backoffice")->insert("insert into cashbook_transactions (type,amount,user_id,motif,cashbook_id,date,add_date,new_balance) values (?,?,?,?,?,?,?,?)",[ $request->input("type"), $request->input("amount"),\Auth::user()->id, $request->input("motif"),$request->input("cashbook"),$date,$real_date,$amt]);
            
            
            $up = \DB::connection("mysql_backoffice")->update("update cash_book set balance=balance$sign? where cashbookid=?",[$request->input("amount"), $request->input("cashbook")]);
           
           if($up>-1){
                \DB::commit();
                return \Redirect::back()->with("date",$date);
            }else {
                 \DB::rollBack();
                 return \Redirect::back()->action('BackofficeController@index')->with("error","Error saving transaction, Please try again");
            }
           
        }catch(Exception $e)
        {
            \DB::rollBack();
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {

    }

    public function show($id)
    {
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
