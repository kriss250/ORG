<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use ORG;
class CalendarViewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    public function DisplayCal()
    {
        $res = DB::connection("mysql")->select("select idreservation,date(checkin) as checkin,date(checkout) as checkout,room_number,concat_ws(' ',firstname,lastname) as Guest,status_name
         from rooms 
        join room_types on type_id = idroom_types
        join room_status on status_code = rooms.status
        left join reserved_rooms on (room_id= idrooms and checked_out is null)
        left join reservations on reservation_id = idreservation and reservations.status=1 
        join guest on id_guest = guest_in
        group by idrooms
        order by checkin asc");
        
        return \View::make('ORGFrontdesk/homeviews/calendarView')->with(array("data"=>$res));
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
