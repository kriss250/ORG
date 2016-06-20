<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;
use App\Http\Controllers\Controller;

class FloorViewController extends Controller
{
    public $res = array();
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {



    }

    public function Display()
    {

        $this->res = DB::connection("mysql")->select("select concat_ws(' ',firstname,lastname) as guest, idrooms,room_number,type_name,floors_id,status_name,floor_name,reservation_id from rooms
        join room_types on type_id = idroom_types
        join room_status on status_code = rooms.status
        join floors on rooms.floors_id = idfloors
        left join reserved_rooms on (room_id= idrooms and checked_out is null and reservation_id=(select reservation_id from reserved_rooms where room_id=idrooms and checked_out is null order by date(checkin) asc  limit 1))
        left join guest on guest.id_guest = guest_in
        left join reservations on reservation_id = idreservation and reservations.status in(1,5) group by idrooms

        order by idfloors");

       return \View::make("ORGFrontdesk/homeviews/floors")->with(array('data'=>$this->res));
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
     * @return Response
     */
    public function store()
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
