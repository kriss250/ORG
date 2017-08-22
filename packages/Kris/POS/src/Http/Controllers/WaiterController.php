<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use \DB,\ORG,\Datatables;

class WaiterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if(isset($_GET['json'])){

            $columns = array(
                array( 'db' => 'idwaiter', 'dt' => 0 ),
                array( 'db' => 'waiter_name', 'dt' => 1 ),
                array( 'db' => 'firstname',  'dt' => 2 ),
                array( 'db' => 'lastname',   'dt' => 3 ),
                array( 'db' => 'date','dt' => 4,
                        'formatter'=>function($d,$row){
                            return date(\ORG\Dates::DSPDATEFORMAT,strtotime($d));
                        }
                    )
            );

            return Datatables\SSP::simple( $_GET, "waiters", "idwaiter", $columns,"" );
           }
        return \View::make("Pos.WaitersList");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        if($request->ajax()) return \View::make("Backoffice.NewWaiter");
        return \View::make("Pos.NewWaiter");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $req)
    {
        $data = $req->all();
        $vl =  \Validator::make($data, [
            'waiter_name' => 'required|min:3|max:255|unique:waiters'
        ]);



       if(count($vl->errors()) == 0)
       {

        $in = DB::insert("insert into waiters (waiter_name,firstname,lastname,date) values(?,?,?,?)",
            [
                $data['waiter_name'],
                $data['firstname'],
                $data['lastname'],
                date(\ORG\Dates::DBDATEFORMAT)
            ]
        );

        if($in>0){
            return json_encode(["errors"=> null,'success'=>1]);
        }

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
         return \View::make("Pos.EditWaiter",["waiter"=>\App\Waiter::find($id)] );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $waiter = \App\Waiter::find($id);
        $waiter->firstname = $_POST['firstname'];
        $waiter->lastname=  $_POST['lastname'];

        if(strlen($_POST['pin']) > 2)
        {
            $waiter->PIN = md5($_POST['pin']);
        }

        if($waiter->save()){
            return json_encode(["success"=>"1"]);
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


    public function changePIN()
    {
        $data = \Request::all();
        if(strlen($data['old_pin'])>0){
            $waiter = \App\Waiter::where("idwaiter",$data['waiterid'])->where("pin",md5($data['old_pin']))->get()->first();
        }else {
            //New Pin
            $waiter = \App\Waiter::where("idwaiter",$data['waiterid'])->where("pin")->get()->first();
        }
        if(strlen($data['new_pin']) < 4) {
             return json_encode(["error"=>"Your PIN must be atleast 4 characters long"]);
        }
        if($waiter == null)
        {
            return json_encode(["error"=>"Invalid PIN"]);
        }else {
            $waiter->update(['pin'=>md5($data['new_pin'])]);
            return json_encode(["success"=>1]);
        }
    }
}
