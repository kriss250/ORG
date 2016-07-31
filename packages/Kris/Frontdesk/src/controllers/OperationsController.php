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
use \Kris\Frontdesk;
class OperationsController extends Controller
{
    public function home($view)
    {
        switch($view)
        {
            case "standard":
                return $this->standardView();
            case "floor":
                return $this->floorsView();
            case "booking":
                return $this->bookingView();
        }
    }

    public function forms($form)
    {
        return call_user_func([$this,"{$form}Form"]);
    }

    public function _print($doc)
    {
        return call_user_func([$this,"{$doc}Doc"]);
    }

    public function billDoc()
    {
        return view("Frontdesk::billDoc");
    }

    public function frame($form)
    {
        return call_user_func([$this,"{$form}"]);
    }

    public function expectedArrival()
    {

        $reservations = null;
        if(\Request::isMethod('post')){
            $reservations = \Kris\Frontdesk\Reservation::where("status",\Kris\Frontdesk\Reservation::ACTIVE)->whereBetween(\DB::raw("date(checkout)"),[\Request::input("fromdate"),\Request::input("todate")])->get();
            return view("Frontdesk::expectedArrival")->with(["reservations"=>$reservations]);
        }else {
            return view("Frontdesk::expectedArrival");
        }
    }

    public function newDay()
    {
           if(\Request::isMethod("post"))
           {
               $date = \Kris\Frontdesk\Env::WD();

                 $toCheckout = \Kris\Frontdesk\Reservation::whereNotNull("checked_in")->whereNull("checked_out")->where(\DB::raw("date(checkout)"),"=",$date->format("Y-m-d"))->get()->count();
                 $toCheckin = \Kris\Frontdesk\Reservation::whereNull("checked_out")->whereNull("checked_in")->whereNotIn("status",[\Kris\Frontdesk\Reservation::CANCELLED,\Kris\Frontdesk\Reservation::NOSHOW])->where(\DB::raw("date(checkin)"),"=",$date->format("Y-m-d"))->get()->count();


                 if($toCheckout > 0 || $toCheckin > 0)
                 {
                     return redirect()->back()->withErrors(["There are guests that need to be checked in or out , to make a new day"]);

                 }
               $sql1 = "insert into night_audit (working_date,user_id,new_date) values ('" .($date->format("Y-m-d")). "',".(\Kris\Frontdesk\User::me()->idusers).",'" .($date->addDay()->format("Y-m-d")). "');";
               $sql2 = "insert ignore into acco_charges (reservation_id,room_id,room_number,amount,date) select idreservation,reservations.room_id,rooms.room_number,night_rate,'".($date)."'
                 from reservations
                join rooms on rooms.idrooms = reservations.room_id
                where checked_out is null and checked_in is not null
                and reservations.status=".(\Kris\Frontdesk\Reservation::CHECKEDIN).";";

               $sql3 = "update rooms
                join reservations on reservations.room_id = idrooms and checked_in is null and checked_out is null
                set rooms.status =".(\Kris\Frontdesk\RoomStatus::RESERVED)." where reservations.status not in (2,3,4,5) and rooms.status <>".(\Kris\Frontdesk\RoomStatus::OCCUPIED)." and date(checkin)='".(\Kris\Frontdesk\Env::WD()->format("Y-m-d"))."'";

               \DB::connection("mysql_book")->insert($sql1);
               \DB::connection("mysql_book")->insert($sql2);
               \DB::connection("mysql_book")->update($sql3);
               \Kris\Frontdesk\Reservation::where("status",\Kris\Frontdesk\Reservation::CHECKEDIN)->whereNotNull("checked_in")->whereNull("checked_out")->update(["due_amount"=>\DB::raw("due_amount+night_rate")]);
               return redirect()->back()->with(["msg"=>"New day Set","refresh"=>1]);
           }

       return view("Frontdesk::newDay");
    }

    public function expectedDeparture()
    {
        $reservations = null;
        if(\Request::isMethod('post')){
            $reservations = \Kris\Frontdesk\Reservation::where("status",\Kris\Frontdesk\Reservation::CHECKEDIN)->whereBetween(\DB::raw("date(checkout)"),[\Request::input("fromdate"),\Request::input("todate")])->get();
            return view("Frontdesk::expectedDeparture")->with(["reservations"=>$reservations]);
        }else {
            return view("Frontdesk::expectedDeparture");
        }

    }

    public function billList()
    {
        if(\Request::isMethod('post'))
        {
            $reservations = \Kris\Frontdesk\Reservation::where("status",\Kris\Frontdesk\Reservation::CHECKEDIN)->whereBetween(\DB::raw("date(checkout)"),[\Request::input("fromdate"),\Request::input("todate")])->get();

            return \View::make("Frontdesk::billList",["reservations"=>$reservations]);
        }
        return \View::make("Frontdesk::billList");
    }

    public function lists($list)
    {
        return call_user_func([$this,"{$list}List"]);
    }

    public function guestDB()
    {
        return view("Frontdesk::guestList");
    }

    public function walkinForm()
    {
        return view("Frontdesk::walkinForm");
    }

    public function addChargeForm()
    {
        return view("Frontdesk::addChargeForm");
    }

    public function makeRefundForm()
    {
        return view("Frontdesk::refundForm");
    }

    public function reservationList()
    {
        return view("Frontdesk::reservationList");
    }

    public function groupReservationForm()
    {
        return view("Frontdesk::groupReservation");
    }

    public function roomView($id)
    {
        $res = \Kris\Frontdesk\Reservation::find($id);
        return view("Frontdesk::roomView")->with("res",$res);
    }

    public function reservationForm()
    {
        return view("Frontdesk::reservationForm");
    }

    public function standardView()
    {
        $sql = "select room_number,type_name,floor_name,status_name,concat_ws(' ',firstname,lastname) as Guest,name,checkin,checkout,phone_ext,status_code,idrooms,idreservation,idroom_types,idfloors from rooms
                        join room_types on type_id = idroom_types
                        join floors on rooms.floors_id = idfloors
                        join room_status on status_code = rooms.status

                        left join reservations on reservations.room_id= rooms.idrooms and checked_out is null and rooms.status in (" . \Kris\Frontdesk\RoomStatus::RESERVED.",".\Kris\Frontdesk\RoomStatus::OCCUPIED.") and reservations.status IN(" .\Kris\Frontdesk\Reservation::ACTIVE . ",".\Kris\Frontdesk\Reservation::CHECKEDIN .") ".
                        "left join guest on guest.id_guest = reservations.guest_id
                        left join companies on company_id = idcompanies
                        group by idrooms
                        order by rooms.status desc";

        $data = \DB::connection("mysql_book")->select($sql);
        return \View::make("Frontdesk::standardView",["data"=>$data]);
    }

    public function floorsView()
    {
        $data = \DB::connection("mysql")->select("select concat_ws(' ',firstname,lastname) as guest, idrooms,room_number,type_name,floors_id,status_name,floor_name,idreservation from rooms
        join room_types on type_id = idroom_types
        join room_status on status_code = rooms.status
        join floors on rooms.floors_id = idfloors

        left join reservations on (room_id= idrooms and checked_out is null) and reservations.status in(1,5)
        left join guest on guest.id_guest = reservations.guest_id
        group by idrooms
        order by idfloors");

        return \View::make("Frontdesk::floors")->with(array('data'=>$data));
    }

    public function bookingView()
    {
        $roomTypes = \DB::connection("mysql_book")->select("select idroom_types as id,type_name,alias from room_types");
        $data = [];
        foreach($roomTypes as $type)
        {
            $rooms = \DB::connection("mysql_book")->select("select idrooms as id,room_number,status_name from rooms join room_status on room_status.status_code = status where type_id=?",[$type->id]);
            $data[$type->alias] = [];

            if(count($rooms)>0)
            {
                foreach($rooms as $room)
                {
                    array_push( $data[$type->alias],$room);
                }
            }

        }
        \ORG\POS::Log("Access Booking View","default");

        return \View::make("Frontdesk::bookingView")->with(array('types'=>$data));
    }


    public function getBookingData()
    {
        $date = $_GET['startdate'];
        $days = $_GET['days'];
        $_date =  new \DateTime($date);
        $new_date = new \DateTime($date);
        $enddate = $new_date->add(new \DateInterval("P{$days}D"))->format("Y-m-d");

        $data = \DB::connection("mysql_book")->select("select concat_ws(' ',firstname,lastname)as guest,room_number,room_id,reservation_status.status_name,idreservation as reservation_id,greatest('{$_date->format("Y-m-d")}',
date_format(checkin,'%Y-%m-%d'))  as checkin,date_format(checkout,'%d_%m') as checkout,datediff(date(checkout),greatest('{$_date->format("Y-m-d")}',
date_format(checkin,'%Y-%m-%d')))-(1) as days from reservations
            join rooms on rooms.idrooms = room_id
join reservation_status on reservation_status.idreservation_status = reservations.status
join guest on guest.id_guest = guest_id
            where reservations.status not in (2,3,4) and date(checkin) <= ? and date(checkout) >=?  order by reservations.status desc ",[$enddate,$date]);

        return json_encode($data);
    }

    public function findCompany()
    {
        $name = "%".\Request::input("name")."%";
        echo \Kris\Frontdesk\Company::where("name","like",$name)->limit(20)->get();
    }

    public function banquet()
    {
        return view("Frontdesk::banquet");
    }

    public function addSale()
    {
        $data ="";
        $errors = [];


        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            if(!is_numeric($data['amount']))
            {
                $errors[] = "Amount must be in number format";
            }

            if($data['mode']=="0" && $data['pay_method'] < 1)
            {
                $errors[] = "Please Payment method";
            }

            $q = "insert into misc_sales (guest,receipt,service,description,amount,pay_mode,is_credit,date,user_id) values(?,?,?,?,?,?,?,?,?)";


            if(count($errors)>0)
            {
               return redirect()->back()->withInput()->withErrors($errors);
            }else
            {
                $id = \Kris\Frontdesk\User::me()->idusers;

                \DB::connection("mysql_book")->insert($q,[$data['names'],$data['receipt'],$data['service'],$data['desc'],$data['amount'],$data['pay_method'],$data['mode'],\Kris\Frontdesk\Env::WD()->format("Y-m-d"),$id]);
               return redirect()->back()->with("msg","Sale saved !");
            }

        }else {
            return \View::make("Frontdesk::addSales");
        }
    }

    public function banquetEventForm()
    {
        return view("Frontdesk::banquetEvent");
    }

    public function addBanquetEvent()
    {
        $data = \Request::all();
        $startdate = new \Carbon\Carbon($data['startdate']);
        $enddate = new \Carbon\Carbon($data['enddate']);

        $count = $enddate->diff($startdate)->days+1;

        $rows ="";
        $startdate->addDays(-1);
        for($i=0;$i<$count;$i++)
        {
            $row = [
            "info"=>\Request::input("desc"),
            "date"=> $startdate->addDays(1)->format("Y-m-d"),
            "banquet_id"=>\Request::input("banquet")
            ];

            $rows[] = $row;
        }


        $in = \DB::connection("mysql_book")->table("banquet_booking")->insert($rows);

        return redirect()->back()->with("msg","Order saved");
    }

    public function salesList()
    {
        $q = "SELECT idmisc_sales,is_credit,guest,receipt,service,method_name,amount,username,misc_sales.date FROM misc_sales
                                join users on users.idusers = user_id
                                left join pay_method on pay_method.idpay_method = pay_mode where date(misc_sales.date) between ? and ?";

        $data = \DB::connection("mysql_book")->select($q,[$_GET['startdate'],$_GET['enddate']]);

        return view("Frontdesk::SalesList")->with("data",$data);
    }

    public function roomStatus()
    {
        return view("Frontdesk::roomStatus");

    }

    public function setRoomStatus()
    {
        return \Kris\Frontdesk\Room::find($_GET['roomid'])->update(["status"=>$_GET['status']]);
    }

    public function newHkTask()
    {
        if(\Request::isMethod("post"))
        {
            $fields = \Request::all();

            $wdate = \Kris\Frontdesk\Env::WD();
            $keys =array_keys($_POST);

            $data = [];
            $userid = \Kris\Frontdesk\User::me()->idusers;
            foreach($keys as $key)
            {
                $parts = explode("_",$key);

                if($parts[0]=="room")
                {
                    $data[] = ["room_id"=>$parts[1],"maid_id"=> $parts[2],"date"=>$wdate->format("Y-m-d"),"user_id"=>$userid];
                }
            }
            \Kris\Frontdesk\Housekeeping::where("date",$wdate->format("Y-m-d"))->delete();
            \Kris\Frontdesk\Housekeeping::insert($data);
        }

        $maids = \Kris\Frontdesk\Maid::all();
        $tasks = \Kris\Frontdesk\Housekeeping::where("date",\Kris\Frontdesk\Env::WD()->format("Y-m-d"))->get();
        return view("Frontdesk::housekeeping.newTask")->with(["maids"=>$maids,"tasks"=>$tasks]);
    }

    public function newLaundry()
    {
        $data = \Request::all();
        if(\Request::isMethod('post'))
        {
            $room  = \Kris\Frontdesk\Room::where("room_number",$data['room'])->where("status",\Kris\Frontdesk\RoomStatus::OCCUPIED)->get()->first();

            if(!is_numeric($data['amount']))
            {
                return redirect()->back()->withInput()->withErrors(["invalid amount"]);
            }


            if($room !=null)
            {
                 $res = \Kris\Frontdesk\Reservation::where("room_id",$room->idrooms)->where("status",\Kris\Frontdesk\Reservation::CHECKEDIN)->get()->first();
                 if($res !=null){
                     if((new \Kris\Frontdesk\Charge)->addCharge($data['amount'],4,"Laundry ".$data['ref'],$res->idreservation))
                     {
                         \DB::connection("mysql_book")->insert("insert into laundry (amount,room_id,reservation_id,user_id,items,reference,date) values(?,?,?,?,?,?,?)",
                             [
                             $data['amount'],
                             $room->idrooms,
                             $res->idreservation,
                             \Kris\Frontdesk\User::me()->idusers,
                             $data['items'],
                             $data['ref'],
                             \Kris\Frontdesk\Env::WD()->format("Y-m-d")
                             ]);
                         return redirect()->back()->with("msg","Order posted to room ".$data['room']);
                     }else {
                         return redirect()->back()->withInput()->withErrors(["Posting to room ".$data['room']." failed"]);
                     }
                 }

            }else {
                return redirect()->back()->withInput()->withErrors(["invalid room"]);
            }
        }else{
            return view("Frontdesk::housekeeping.newLaundry");
        }
    }
}