<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CashbookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data = \DB::connection("mysql_backoffice")->select("select * from cash_book order by cashbookid asc");
        return \View::make("Backoffice.listCashbooks",["data"=>$data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return \View::make("Backoffice.AddCashBook");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $name = $request->input("bookname");
        $balance = $request->input("balance");
        $ins = \DB::connection("mysql_backoffice")->insert("insert into cash_book (cashbook_name,balance) values(?,?)",[$name,$balance]);

        if($ins)
        {
            return redirect()->action('BackofficeController@index')->with("status","Cash book created successfully");
        }else {
            return redirect()->action('BackofficeController@index')->with("error","Error creating cashbook, Please try again");
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
        
      
        $cashbook = \DB::connection("mysql_backoffice")->select("select cashbookid,cashbook_name,balance from cash_book where   cashbookid=?",[$id])[0];

        $params = [$id];
        $where = "";
        
        if(isset($_GET['date']) && strlen($_GET['date'])>2)
        {
            $d = explode('/', $_GET['date']);

            $date = $d[2]."-".$d[1]."-".$d[0];
            $where = " and date(cashbook_transactions.date)=?";
            $params = [$id,$date];
        }else{
            $dv_date = \Session::get('date');
            
            $params = [$id,\ORG\Dates::$RESTODATE];
            
            if(strlen($dv_date)>6)
            {
                $params = [$id,$dv_date];
            }
            
            $where = " and date(cashbook_transactions.date)=?";
        }
        
        $date = new \DateTime($params[1]);
        $date->sub(new \DateInterval('P1D'));
        $prev_date = $date->format('Y-m-d');
        
        
        
        $prev_amount = \DB::connection("mysql_backoffice")->select("SELECT new_balance FROM cashbook_transactions where deleted=0 and date(date)=? and cashbook_id=? and recorded=0 order by transactionid desc limit 1",[$prev_date,$id]);

 
        $initial = isset($prev_amount[0]->new_balance) ? $prev_amount[0]->new_balance : 0 ;
        $transactions = \DB::connection("mysql_backoffice")->select("select transactionid,new_balance,type,amount,motif,username,cashbook_transactions.date from cashbook_transactions join org_pos.users on org_pos.users.id = user_id where cancelled=0 and cashbook_id=? and deleted=0 $where order by transactionid asc",$params);

        return \View::make("Backoffice.openCashbook",['cashbook'=>$cashbook,"transactions"=>$transactions,"initial"=>$initial]);
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
    
}
