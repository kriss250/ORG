<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if(isset($_GET['markasseen']))
        {
            $sql = "insert into user_seen_annoucement (user_id,announcement_id,date) values (?,?,?)";
            \DB::connection("mysql_backoffice")->insert($sql,[\Auth::user()->id,$_GET['announcement'],date('Y-m-d')]);
            return true;
        }
        
        $an = \DB::connection("mysql_backoffice")->select("select title,body,announcements.date,username from announcements join org_pos.users on users.id=user_id");
        return \View::make("Backoffice.listAnnouncements",["data"=>$an]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return \View::make("Backoffice.addAnnouncement");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $title = $request->input("title","");
        $message = $request->input("message");
        $user_id = \Auth::user()->id;
        $date = date("Y-m-d H:i:s");
        
        $ins =  \DB::connection("mysql_backoffice")->insert("insert into announcements (title,body,user_id,date) values (?,?,?,?)",[$title,$message,$user_id,$date]);
        
        if($ins){
            return redirect()->action('BackofficeController@index')->with("status","Announcement Saved");
        }else {
            return redirect()->action('BackofficeController@index')->with("error","Error Saving the Announcement, Please try again");
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
