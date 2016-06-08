<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class BookingViewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roomTypes = \DB::connection("mysql_book")->select("select idroom_types as id,type_name from room_types");
        $data = [];
        foreach($roomTypes as $type)
        {
            $rooms = \DB::connection("mysql_book")->select("select idrooms as id,room_number,status_name from rooms join room_status on room_status.status_code = status where type_id=?",[$type->id]);
            $data[$type->type_name] = [];

          if(count($rooms)>0)
          {
              foreach($rooms as $room)
              {
                  array_push( $data[$type->type_name],$room);
              }
          }

        }
        \ORG\POS::Log("Access Booking View","default");
        return \View::make("ORGFrontdesk.homeviews.BookingView",["types"=>$data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function getBookingData()
    {
        $date = $_GET['startdate'];
        $days = $_GET['days'];
        $_date =  new \DateTime($date);
        $new_date = new \DateTime($date);
        $enddate = $new_date->add(new \DateInterval("P{$days}D"))->format("Y-m-d");

        $data = \DB::connection("mysql_book")->select("select concat_ws(' ',firstname,lastname)as guest,room_number,room_id,reservation_status.status_name,reservation_id,greatest('{$_date->format("Y-m-d")}',
date_format(checkin,'%Y-%m-%d'))  as checkin,date_format(checkout,'%d_%m') as checkout,datediff(date(checkout),greatest('{$_date->format("Y-m-d")}',
date_format(checkin,'%Y-%m-%d'))) as days from reserved_rooms
            join reservations on reservations.idreservation = reservation_id
            join rooms on rooms.idrooms = room_id
join reservation_status on reservation_status.idreservation_status = reservations.status
join guest on guest.id_guest = guest_in
            where reservations.status not in (2,3,4) and date(checkin) <= ? and date(checkin)<=? and date(checkout) >=?  order by reservations.status desc ",[$enddate,$date,$date]);

        return json_encode($data);
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
}
