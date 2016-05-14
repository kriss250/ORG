<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use \ORG;
use \Datatables;

class UsersController extends Controller
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
                array( 'db' => 'id', 'dt' => 0 ),
                array( 'db' => 'username', 'dt' => 1 ),
                array( 'db' => 'firstname',  'dt' => 2 ),
                array( 'db' => 'lastname',   'dt' => 3 ),
                array( 'db' => 'level',     'dt' => 4 ),
                array( 'db' => 'date','dt' => 5,
                        'formatter'=>function($d,$row){
                            return date(ORG\Dates::DSPDATEFORMAT,strtotime($d));
                        }      
                    )
            );

            return Datatables\SSP::simple( $_GET, "users", "id", $columns,"" );
            
        } 

        return \View::make("Pos.UsersList");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        if($request->ajax()) return \View::make("Backoffice.NewUser");
        return \View::make("Pos.NewUser");
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
            'username' => 'required|max:255|unique:users',
            'password' => 'required|min:6',
            'level'=>"required|numeric"
        ]);



       if(count($vl->errors()) == 0)
       {

        $in = DB::insert("insert into users (username,password,firstname,lastname,level,date) values(?,?,?,?,?,?)",
            [
                $data['username'],
                \Hash::make($data['password']),
                $data['firstname'],
                $data['lastname'],
                $data['level'],
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
