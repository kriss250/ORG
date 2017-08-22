<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class UniversalUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $systems  =\App\User::$Systems;
        $pos_users = \DB::select("select id,firstname,lastname,username,level,date,is_active,'POS' as system from users limit 60");
        $stock_users= \DB::connection("mysql_stock")->select("select users.id,first_name as firstname,last_name as lastname,username,'1' as is_active,groups.name as group_name,created_on,'Stock' as system from users join users_groups on users_groups.user_id = users.id
 join groups on groups.id=group_id");
        //$backoffice_users = "";
        $frontdesk_users = \DB::connection("mysql_book")->select("select idusers as id,firstname,lastname,username,users.date,user_group.name as group_name,is_active,'Frontdesk' as system from users join user_group on user_group.iduser_group = users.group_id limit 60");

        return \View::make("Backoffice.ListUsers",["fo_users"=>$frontdesk_users,"stock_users"=>$stock_users,"pos_users"=>$pos_users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return \View::make("Backoffice.NewUser");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return \View::make("Backoffice.UserEdit");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function activationToggle($userid)
    {
        $action = $_GET['action'];
        $system = $_GET['system'];


        switch($action)
        {
            case "activate":
                if(strtolower($system)=="pos")
                {

                    $up = \App\User::find($userid);
                    $up->is_active=1;
                    $up->save();

                }else if(strtolower($system)=="frontdesk")
                {
                   \Kris\Frontdesk\User::find($userid)->update(['is_active'=>"1"]);
                }
                    break;
            case "deactivate":
                if(strtolower($system)=="pos")
                {
                    $up = \App\User::find($userid);
                    $up->is_active=0;
                    $up->save();

                }else if(strtolower($system)=="frontdesk")
                {
                    \Kris\Frontdesk\User::find($userid)->update(['is_active'=>"0"]);
                }
                break;
        }

        return redirect()->back();
    }
}
