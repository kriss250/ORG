<?php

/**
 * OperationsController short summary.
 *
 * OperationsController description.
 *
 * @version 1.0
 * @author kris
 */
namespace Kris\Frontdesk\Controllers;
use App\Http\Controllers\Controller;
use \DB;

class ReservationsController extends Controller
{

    public function walkin(\Request $req)
    {
        $data = $req::all();
        $errors = [];
        $msg = "";
        $res = null;
        \DB::beginTransaction();

        if(!$req::has("room"))
        {
            $errors[]= "Please choose a room";
        }

        if(strlen($data['firstname'])<2)
        {
            $errors[]= "Firstname must be at least 2 character long";
        }

        if(strlen($data['lastname'])<2)
        {
            $errors[]= "Lastname must be at least 2 character long";
        }

        if(!is_numeric($data['rate']))
        {
            $errors[]= "Rate must be in number format";
        }

        if($data['country']=="")
        {
            $errors[]= "Please choose a country";
        }

        if(strlen($data['checkout']) < 4)
        {
            $errors[]= "Invalid checkout date";
        }

        $checkin  = new \Carbon\Carbon($data['checkin']);
        $checkout  = new \Carbon\Carbon($data['checkout']);

        if($checkin->gte($checkout) || $checkin->lt(\Kris\Frontdesk\Env::WD()))
        {
            $errors[]= "Invalid Checkin/Checkout dates";
        }



        $guest_uid = md5(trim(strtolower($data['firstname'])) . trim(strtolower($data['lastname'])) . trim(strtolower($data['email'])));


        $room = isset($data['room']) ?  \Kris\Frontdesk\Room::where([
                "idrooms"=> $data["room"],
                "status"=>\Kris\Frontdesk\RoomStatus::VACANT
                ])->get()->first() : null;

        if(isset($data['room']) && !(new \Kris\Frontdesk\Room)->isAvailable($data['room'],$data['checkin'],$data['checkout']))
        {
            $errors[] = "The selected room is not available";
        }

        if(is_null($room))
        {
            $errors[] = "The selected room is not available";
        }

        if(empty($errors))
        {
            $g = \Kris\Frontdesk\Guest::where("guest_uid",$guest_uid)->get()->first();
            if(is_null($g))
            {
                //Create Guest
                $g = \Kris\Frontdesk\Guest::create([
                    "firstname"=>$data['firstname'],
                    "lastname"=>$data['lastname'],
                    "phone"=>$data['phone'],
                    "email"=>$data['email'],
                    "birthdate"=>$data['birthdate'],
                    "guest_uid"=>$guest_uid,
                    "country"=>$data['country'],
                    "id_doc"=>$data['passport']
                    ]);
            }
            //Create Company
            $c = null;

            if(strlen($data['company'])>1){
                $c = \Kris\Frontdesk\Company::where("name",$data['company'])->get()->first();
                if(is_null($c))
                {
                    $c = \Kris\Frontdesk\Company::create([
                        "name"=>$data['company']
                        ]);
                }
            }
            //Create businessSource

            ///Save reservation
            // ! credit = 1 payment = 0
            $res = \Kris\Frontdesk\User::me()->reservation()->create([
                "checkin"=>$data['checkin'],
                "checkout"=>$data['checkout'],
                "adults"=>$data['adults'],
                "children"=>$data['children'],
                "night_rate"=>$data['rate'],
                "date"=>\Kris\Frontdesk\Env::WD()->format("Y-m-d"),
                "company_id"=> is_null($c) ? 0 : $c->idcompanies,
                "rate_id"=>$data['rate_type'],
                "business_source"=>1,
                "payer"=> is_null($c) ? "SELF" : $c->name,
                "pay_by_credit"=> $data['mode'],
                "prefered_pay_mode"=>$data['pay_method'],
                "checked_in"=>\Kris\Frontdesk\Env::WD()->format("Y-m-d"),
                "room_id"=> $room->idrooms,
                "status"=> 5,
                "guest_id"=>$g->id_guest,
                "breakfast"=>1,
                "package_name"=>$data['package'],
                "checkedin_by"=>\Kris\Frontdesk\User::me()->idusers,
                ]);

            //update room status
            if($room != null){
                $room->update(["status"=>\Kris\Frontdesk\RoomStatus::OCCUPIED]);
            }
        }

        if($res != null)
        {
            \DB::commit();
            \FO::log("Guest Walkin");
            $msg = "Guest checked in successfully  # code ".$res->idreservation;
        }else {
            \DB::rollBack();
        }

        $resp = new \stdClass();
        $resp->errors = $errors;
        $resp->msg = $msg;

        return json_encode($resp);
    }

    public function reserve(\Request $req)
    {
        $data = $req::all();
        $errors = [];
        $msg = "";
        $res = null;
        \DB::beginTransaction();

        if(!$req::has("room"))
        {
            $errors[]= "Please choose a room";
        }

        if(strlen($data['firstname'])<2)
        {
            $errors[]= "Firstname must be at least 2 character long";
        }

        if(strlen($data['lastname'])<2)
        {
            $errors[]= "Lastname must be at least 2 character long";
        }

        if(!is_numeric($data['rate']))
        {
            $errors[]= "Rate must be in number format";
        }

        if($data['country']=="")
        {
            $errors[]= "Please choose a country";
        }

        if($data['mode']==0 && $data['pay_method'] <1)
        {
            $errors[]= "Please choose payment method";
        }

        if(strlen($data['checkout']) < 4)
        {
            $errors[]= "Invalid checkout date";
        }

        $checkin  = new \Carbon\Carbon($data['checkin']);
        $checkout  = new \Carbon\Carbon($data['checkout']);

        if($checkin->gte($checkout) || $checkin->lt(\Kris\Frontdesk\Env::WD()))
        {
            $errors[]= "Invalid Checkin/Checkout dates";
        }



        $guest_uid = md5(trim(strtolower($data['firstname'])) . trim(strtolower($data['lastname'])) . trim(strtolower($data['email'])));

        if(isset($data['room'])){
            if(!(new \Kris\Frontdesk\Room)->isAvailable($data['room'],$data['checkin'],$data['checkout']))
            {
                $errors[] = "The selected room is not available";
            }
        }

        if(empty($errors))
        {
            $g = \Kris\Frontdesk\Guest::where("guest_uid",$guest_uid)->get()->first();
            if(is_null($g))
            {
                //Create Guest

                $g = \Kris\Frontdesk\Guest::create([
                    "firstname"=>$data['firstname'],
                    "lastname"=>$data['lastname'],
                    "phone"=>$data['phone'],
                    "email"=>$data['email'],
                    "birthdate"=>$data['birthdate'],
                    "guest_uid"=>$guest_uid,
                    "country"=>$data['country'],
                    "id_doc"=>$data['passport']
                    ]);
            }
            //Create Company
            $c = null;

            if(strlen($data['company'])>1){
                $c = \Kris\Frontdesk\Company::where("name",$data['company'])->get()->first();
                if(is_null($c))
                {
                    $c = \Kris\Frontdesk\Company::create([
                        "name"=>$data['company']
                        ]);
                }
            }
            //Create businessSource

            ///Save reservation
            // ! credit = 1 payment = 0
            $res = \Kris\Frontdesk\User::me()->reservation()->create([
                "checkin"=>$data['checkin'],
                "checkout"=>$data['checkout'],
                "adults"=>$data['adults'],
                "children"=>$data['children'],
                "night_rate"=>$data['rate'],
                "date"=>\Kris\Frontdesk\Env::WD()->format("Y-m-d"),
                "company_id"=> is_null($c) ? 0 : $c->idcompanies,
                "rate_id"=>$data['rate_type'],
                "business_source"=>1,
                "payer"=> is_null($c) ? "SELF" : $c->name,
                "pay_by_credit"=> $data['mode'],
                "prefered_pay_mode"=>$data['pay_method'],
                "room_id"=> $data['room'],
                "status"=> 1,
                "breakfast"=>1,
                "guest_id"=>$g->id_guest,
                "package_name"=>$data['package']
                ]);

            //update room status
            if($checkin->eq(\Kris\Frontdesk\Env::WD())){
                 \Kris\Frontdesk\Room::find($data['room'])->update(["status"=>\Kris\Frontdesk\RoomStatus::RESERVED]);
            }

        }

        if($res != null)
        {
            \DB::commit();
            \FO::log("Created reservation");
            $msg = "Reservation saved successfully  # code ".$res->idreservation;
        }else {
            \DB::rollBack();
        }

        $resp = new \stdClass();
        $resp->errors = $errors;
        $resp->msg = $msg;

        return json_encode($resp);
    }

    public function reserveGroup(\Request $req)
    {
        $data = $req::all();
        $rooms = [];
        $roomIds = [];
        foreach($data as $key=>$val)
        {
            $parts = explode("_",$key);


            if(count($parts)>0 && $parts[0]=="room")
            {
                if($parts[1] < 1)
                {
                    continue;
                }
                //Save the room
                $theRoom = new  \stdClass();
                $theRoom->id = $val;
                $theRoom->rate = $data['rate_'.$parts[1]];
                $rooms[] = $theRoom;
                $roomIds[] = $val;
            }
        }

        $errors = [];
        $msg = "";
        $res = null;
        \DB::beginTransaction();

        if(count($rooms) < 1)
        {
            $errors[]= "Please choose at least one room";
        }

        if($data['mode']==0 && $data['pay_method'] <1)
        {
            $errors[]= "Please choose payment method";
        }

        if(strlen($data['checkout']) < 4)
        {
            $errors[]= "Invalid checkout date";
        }

        $checkin  = new \Carbon\Carbon($data['checkin']);
        $checkout  = new \Carbon\Carbon($data['checkout']);

        if($checkin->gte($checkout) || $checkin->lt(\Kris\Frontdesk\Env::WD()))
        {
            $errors[]= "Invalid Checkin/Checkout dates";
        }

        if(empty($errors))
        {

            //Create Company
            $c = null;

            if(strlen($data['company'])>1){
                $c = \Kris\Frontdesk\Company::where("name",$data['company'])->get()->first();
                if(is_null($c))
                {
                    $c = \Kris\Frontdesk\Company::create([
                        "name"=>$data['company']
                        ]);
                }
            }

            //create the group

            $group = \Kris\Frontdesk\ReservationGroup::create([
                "group_name"=>$data['group_name'],
                "company_id"=> is_null($c) ? 0 : $c->idcompanies,
                "arrival"=>$data['checkin'],
                "departure"=>$data['checkout'],
                "adults"=> $data['adults'],
                "children"=>$data['children'],
                "date"=>\Kris\Frontdesk\Env::WD()->format("Y-m-d"),
                "created_by"=>\Kris\Frontdesk\User::me()->idusers,
                "is_credit"=>$data['mode'],
                "prefered_pay_method"=>$data['pay_method']
                ]);

            //Create businessSource

            ///Save reservation
            // ! credit = 1 payment = 0

            foreach ($rooms as $room)
            {
            	if(!(new \Kris\Frontdesk\Room)->isAvailable($room->id,$data['checkin'],$data['checkout']))
                {
                    $errors[] = "The selected room is not available";
                    continue;
                }else {

                    $res = \Kris\Frontdesk\User::me()->reservation()->create([
                        "checkin"=>$data['checkin'],
                        "checkout"=>$data['checkout'],
                        "adults"=>$data['adults'],
                        "children"=>$data['children'],
                        "night_rate"=>$room->rate,
                        "date"=>\Kris\Frontdesk\Env::WD()->format("Y-m-d"),
                        "company_id"=> is_null($c) ? 0 : $c->idcompanies,
                        "rate_id"=>$data['rate_type'],
                        "business_source"=>1,
                        "payer"=> is_null($c) ? "SELF" : $c->name,
                        "pay_by_credit"=> $data['mode'],
                        "prefered_pay_mode"=>$data['pay_method'],
                        "room_id"=> $room->id,
                        "status"=> 1,
                        "guest_id"=>0,
                        "group_id"=>$group->groupid,
                        "package_name"=>$data['package']
                        ]);
                }
            }
            //update room status
            if($checkin->eq(\Kris\Frontdesk\Env::WD())){
                \Kris\Frontdesk\Room::whereIn("idrooms",$roomIds)->update(["status"=>\Kris\Frontdesk\RoomStatus::RESERVED]);
            }

        }

        if($res != null)
        {
            \DB::commit();
            \FO::log("Created reservation");
            $msg = "Reservation saved successfully  # code ".$res->idreservation;
        }else {
            \DB::rollBack();
        }

        $resp = new \stdClass();
        $resp->errors = $errors;
        $resp->msg = $msg;

        return json_encode($resp);
    }
    public static function getAvailableRooms()
    {
        $checkin  = \Request::input("checkin");
        $checkout = \Request::input("checkout");
        $roomType = \Request::input("type",0);
        $floor = \Request::input("floor",0);
        return (new \Kris\Frontdesk\Room())->availableRooms($checkin,$checkout,$roomType,$floor);
    }

    public function listReservations()
    {
        $reservations  = \Kris\Frontdesk\Reservation::where("status",\Request::input("status"))->whereBetween("checkin",[\Request::input("fromdate"),\Request::input("todate")])->get() ;
        return view("Frontdesk::reservationList",["reservations"=>$reservations]);
    }

    public function checkin($id,$roomid)
    {

        $msg = "";
        $error = "";


        $res  = \Kris\Frontdesk\Reservation::where(["status"=>\Kris\Frontdesk\Reservation::ACTIVE,"idreservation"=>$id,"room_id"=>$roomid])->get()->first();
        $room  = \Kris\Frontdesk\Room::find($roomid);

        if($res->guest == null)
        {
            return redirect()->back()->withErrors(["Please update the guest s name first !"]);
        }
        if($res != null && $room !=null)
        {

            $res->checked_in = \Kris\Frontdesk\Env::WD()->format("Y-m-d")." ".date("H:i:s");
            $res->checkedin_by = \Kris\Frontdesk\User::me()->idusers;

            $res->status = \Kris\Frontdesk\Reservation::CHECKEDIN;
            $room->status = \Kris\Frontdesk\RoomStatus::OCCUPIED;

            //Early checkin
            if(\Kris\Frontdesk\Env::WD()->lt(new \Carbon\Carbon($res->checkin)))
            {
                //It's an Early Checkin
                $res->checkin = \Kris\Frontdesk\Env::WD()->format("Y-m-d");

                if(\Kris\Frontdesk\Env::WD()->gte(new \Carbon\Carbon($res->checkout)))
                {
                    $res->checkout = \Kris\Frontdesk\Env::WD()->addDay()->format("Y-m-d");
                }
            }

            DB::beginTransaction();

            $saved  = false;
            if($saved = $res->save())
            {
                $saved = $room->save();
            }

            if($saved)
            {
                DB::commit();
                \FO::log("Guest checkin ".$res->room->room_number);
                $msg = "Guest checked in";
            }else {
                $error = "Checkin failed";
                DB::rollBack();
            }
        }else {
            $saved = false;
            $error = "Invalid checkin";
        }

        if($saved){
            return redirect()->back()->with(["refresh"=>1,"msg"=>$msg]);
        }else {
            return redirect()->back()->withErrors([$error]);
        }
    }

    public function checkout($id,$roomid)
    {

        $msg = "";
        $error = "";

        $res  = \Kris\Frontdesk\Reservation::where(["status"=>\Kris\Frontdesk\Reservation::CHECKEDIN,"idreservation"=>$id,"room_id"=>$roomid])->get()->first();
        $room  = \Kris\Frontdesk\Room::find($roomid);

        if($res != null && $room !=null)
        {

            $res->checked_out = \Kris\Frontdesk\Env::WD()->format("Y-m-d")." ".date("H:i:s");
            $res->checkedout_by = \Kris\Frontdesk\User::me()->idusers;

            $res->status = \Kris\Frontdesk\Reservation::CHECKEDOUT;
            $res->last_check_out = \Kris\Frontdesk\Env::WD()->format("Y-m-d")." ".date("H:i:s");
            $room->status = \Kris\Frontdesk\RoomStatus::CHECKEDOUT;

            //Early checkout
            if(\Kris\Frontdesk\Env::WD()->lt(new \Carbon\Carbon($res->checkout)))
            {
                //It's an Early Checkout
                $res->checkout = \Kris\Frontdesk\Env::WD()->format("Y-m-d");
            }

            DB::beginTransaction();

            $saved  = false;

            //check balance
            if($res->due_amount == $res->paid_amount || $res->pay_by_credit==1){
                if($saved = $res->save())
                {
                    $saved = $room->save();
                }
            }else
            {
                $saved = false;
                $error = "Balance must be equal to zero to perform checkout";
            }

            if($saved)
            {
                DB::commit();
                \FO::log("Guest checkout ".$res->room->room_number);
                $msg = "Guest checked out";
            }else
            {
                $error = strlen($error) > 0 ? $error : "Checkout failed";
                DB::rollBack();
            }
        }else
        {
            $saved = false;
            $error = "Invalid checkout";
        }

        if($saved){
            return redirect()->back()->with(["refresh"=>1,"msg"=>$msg]);
        }else {
            return redirect()->back()->withErrors([$error]);
        }
    }

    public function update()
    {
        $res = \Kris\Frontdesk\Reservation::find(\Request::input("_id"));
        $data = \Request::all();

        $checkin=null;
        $checkout=$data['checkout'];

        $currentCheckin = new \Carbon\Carbon($res->checkin);
        $currentCheckout = new \Carbon\Carbon($res->checkout);

        $newCheckin = new \Carbon\Carbon($data['checkin']);
        $newCheckout = new \Carbon\Carbon($data['checkout']);

        //Limit checkin update
        if($res->status == \Kris\Frontdesk\Reservation::ACTIVE)
        {
            $checkin = $data["checkin"];
        }else {
            $checkin = $res->checkin;
        }

        if(!$newCheckin->eq($currentCheckin) || !$newCheckout->eq($currentCheckout) )
        {
            //update dates

            //If the room is available
            if((new \Kris\Frontdesk\Room)->isAvailable($res->room_id,$checkin,$checkout,$res->idreservation))
            {
                $res->checkin = $checkin;
                $res->checkout  = $checkout;
            }
        }

        //update checkout !
        $res->night_rate = $data['rate'];
        $res->rate_id = $data['rate_type'];
        $res->package_name =$data['package'];
        $res->adults = $data['adults'];
        $res->children = $data['children'];
        $res->pay_by_credit = $data['mode'];
        $res->prefered_pay_mode = $data['pay_method'];

        $names = explode(" ",trim($data['names']),2);

        //guest
        if($res->guest !=null && !($res->guest->firstname != $names[0] && $res->guest->lastname !=$names[1] ))
        {
            $res->guest->firstname = $names[0];
            $res->guest->lastname = $names[1];
            $res->guest->phone = $data['phone'];
            $res->guest->email = $data['email'];
            $res->guest->id_doc = $data['id_doc'];
            $res->guest->country = $data['country'];
            $res->guest->city  = $data['city'];
            $res->guest->save();
        }else {

            //Create the guest
            $guest_uid = md5(trim($data['names']) ." ". trim(strtolower($data['email'])));

            $guest = \Kris\Frontdesk\Guest::create([
                "firstname"=> $names[0],
                "lastname"=> $names[1],
                "phone"=>$data['phone'],
                "email"=>$data['email'],
                "country"=>$data["country"],
                "guest_uid"=>$guest_uid,
                "id_doc"=>$data['id_doc'],
                "city"=>$data['city']
                ]);
            $res->guest_id = $guest->id_guest;
        }

        //Update existing company
        if($res->company!=null && $res->company->name != $data['company'])
        {
            $ex = \Kris\Frontdesk\Company::where("name",$data['company'])->get()->first();
            if($ex == null && !isset($ex->idcompanies))
            {
                $res->company->name = $data['company'];
                $res->company->save();
                $res->payer = strlen($data['company']) > 0 ? $data['company'] : "SELF";
            }else {
                //use existing one
                $res->company_id = $ex->idcompanies;
                $res->payer = strlen($data['company']) > 0 ? $data['company'] : "SELF";
            }

        }

        //Create a new company
        if($res->company == null)
        {
            if(strlen($data['company'])>1){
                $cp = \Kris\Frontdesk\Company::firstOrNew([
                    "name"=>$data['company']
                    ]);
                $cp->save();
                $res->company_id = $cp->idcompanies;
                $res->payer = $data['company'];
            }else {
                $res->payer = "SELF";
            }
        }

        $res->save();
        \FO::log("Updated reservation ".$res->idreservation);
        return redirect()->back()->with("refresh","1");
    }

    public function addPayment($id)
    {
        $data = \Request::all();
        $errors =\Validator::make($data, [
            'pay_method' => 'required',
            'amount' => 'required|numeric',
            'currency'=>"required"
         ]);

        $currency = \Kris\Frontdesk\Currency::find($data['currency']);

        if(count($errors->errors()) ==0)
        {
             \Kris\Frontdesk\Payment::create([
                "credit"=>$data["amount"]*$currency->rate,
                "motif"=>$data["motif"],
                "paymethod"=>$data["pay_method"],
                "user_id"=>\Kris\Frontdesk\User::me()->idusers,
                "reservation_id"=>$id,
                "original_amount"=>$data['amount'],
                "currency_id"=>$currency->idcurrency,
                "date"=>\Kris\Frontdesk\Env::WD()->format("Y-m-d")." ".date("H:i:s"),
                ]);

             $up = \Kris\Frontdesk\Reservation::find($id)->update(["paid_amount"=> \DB::raw("paid_amount+".($data["amount"]*$currency->rate) )]);
            if($up)
            {
                //\FO::log("Added payment");
                return redirect()->back()->with("msg","Payment saved");
            }
        }else {
            return redirect()->back()->withErrors($errors->errors());
        }

    }


     public function addRefund($id)
    {
        $data = \Request::all();
        $errors =\Validator::make($data, [
            'amount' => 'required|numeric',
         ]);

        if($data["amount"]<0)
        {
            return;
        }

        if(count($errors->errors()) ==0)
        {
             \Kris\Frontdesk\Payment::create([
                "credit"=>0,
                "debit"=>$data["amount"],
                "motif"=>$data["motif"],
                "paymethod"=>0,
                "user_id"=>\Kris\Frontdesk\User::me()->idusers,
                "reservation_id"=>$id,
                "date"=>\Kris\Frontdesk\Env::WD()->format("Y-m-d")." ".date("H:i:s"),
                ]);

            $up = \Kris\Frontdesk\Reservation::find($id)->update(["paid_amount"=> \DB::raw("paid_amount-".$data["amount"])]);
            if($up)
            {
                \FO::log("Added refund to ".$id);
                return redirect()->back()->with("msg","Refund saved");
            }
        }else {
            return redirect()->back()->withErrors($errors->errors());
        }

    }
    public function shiftRoom($id)
    {
        $data = \Request::all();

        $errors =\Validator::make($data, [
            'new_room' => 'required',
            'new_rate' => 'required|numeric'
         ]);

        $new_room =\Request::input("new_room");

        $the_room = \Kris\Frontdesk\Room::where("room_number",$new_room)->get()->first();
        $new_room_id= $the_room != null ? $the_room->idrooms : 0;
        $res = \Kris\Frontdesk\Reservation::find($id);

        $checkout = $res->checkout;

        if($the_room != null && $res !=null){
            if($res->status == \Kris\Frontdesk\Reservation::CHECKEDIN)
            {
                $checkin = \Kris\Frontdesk\Env::WD()->format("Y-m-d");

                if($the_room->status != \Kris\Frontdesk\RoomStatus::VACANT)
                {
                    $errors->getMessageBag()->add('Room', 'The specified room must be vacant');
                }

            }else {
                $checkin =(new \Carbon\Carbon($res->checkin))->format("Y-m-d");
            }

            //check availability
            if((new \Kris\Frontdesk\Room)->isAvailable($new_room_id,$checkin,$checkout))
            {
                $query = "insert into room_shift (from_room,to_room,user_id,date,from_roomnumber,to_roomnumber,from_roomtype,to_roomtype,reservation_id,motif,new_rate,from_rate)
                values (?,?,?,?,?,?,?,?,?,?,?,?)";
                $userid = \Kris\Frontdesk\User::me()->idusers;
                $date = \Kris\Frontdesk\Env::WD()->format("Y-m-d");

                \DB::connection("mysql_book")->insert($query,
                    [
                    $res->room_id,$new_room_id,$userid,$date,$data['prev_room_number'],$new_room,$res->room->type->type_name,$the_room->type->type_name,$res->idreservation,$data['motif'],$data['new_rate'],$res->night_rate
                    ]);


                //Update status
                $the_room->status = $res->room->status; // new room
                $res->room->status = \Kris\Frontdesk\RoomStatus::VACANT; // old room


                $res->room_id = $new_room_id; //switch rooms
                $res->night_rate = $data['new_rate'];
                $res->room->save();
                $the_room->save();
                $res->save();
            }else {
                $errors->getMessageBag()->add('Room', 'The specified room is not available');
            }
        }else {
            //Invalid info
            $errors->getMessageBag()->add('Room', 'Invalid information');
        }


        if(count($errors->errors()) ==0)
        {
            return redirect()->back()->with("msg","Guest shifted successfully");
        }else {
            return redirect()->back()->withErrors($errors->errors());
        }
    }

    public function addCharge($id)
    {
        $data = \Request::all();

        if(!is_numeric($data['amount']))
        {
            return redirect()->back()->withErrors(["Invalid amount"]);
        }else {
            $amount = $data['amount'];
            if((new \Kris\Frontdesk\Charge)->addCharge($amount,$data['charge_type'],$data['motif'],$id))
            {
                \FO::log("Added room charge");
                return redirect()->back()->with("msg","Charge added");

            }else {
                return redirect()->back()->withErrors(["Adding charge failed"]);
            }

        }
    }

    public function cancel($id)
    {
        $res = \Kris\Frontdesk\Reservation::find($id);
        $res->cancel();

        return redirect()->back()->with(["msg"=>"Reservation cancelled","refresh"=>"1"]);
    }

    public function noshow($id)
    {
        $res = \Kris\Frontdesk\Reservation::find($id);
        $res->noshow();

        return redirect()->back()->with(["msg"=>"Reservation marked as no show","refresh"=>"1"]);
    }
}