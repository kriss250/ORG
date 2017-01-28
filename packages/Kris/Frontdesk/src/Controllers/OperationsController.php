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

    public function frame($form,$ID=null)
    {
        return call_user_func([$this,"{$form}"],$ID);
    }

    public function expectedArrival()
    {
        $reservations = null;
        if(\Request::isMethod('post')){
            $reservations = \Kris\Frontdesk\Reservation::where("status",\Kris\Frontdesk\Reservation::ACTIVE)->whereBetween(\DB::raw("date(checkin)"),[\Request::input("fromdate"),\Request::input("todate")])->get();
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
                set rooms.status =".(\Kris\Frontdesk\RoomStatus::RESERVED)." where reservations.status not in (2,3,4,5) and rooms.status <>".(\Kris\Frontdesk\RoomStatus::OCCUPIED)." and date(checkin)='".($date->format("Y-m-d"))."'";

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
            $reservations = \Kris\Frontdesk\Reservation::where("status","<>",\Kris\Frontdesk\Reservation::CANCELLED)->whereBetween(\DB::raw("date(checkout)"),[\Request::input("fromdate"),\Request::input("todate")])->limit("100")->get();

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
        if(isset($_GET['guest_name']))
        {

            $guestNames = explode(" ",$_GET['guest_name']);

            $firstname = isset($guestNames[0])  ? $guestNames[0] : "";
            $lastname = isset($guestNames[1])  ? $guestNames[1] : "";

            $firstname = "%".$firstname."%";
            $lastname = "%".$lastname."%";
            $names = [$firstname,$lastname,$firstname,$lastname];

            $guests= \Kris\Frontdesk\Guest::whereRaw("(firstname like ? and lastname like ?) or (lastname like ? and firstname like ?)")->setBindings($names)->get();
            return \View::make("Frontdesk::guestList",["guests"=>$guests]);
        }

        return view("Frontdesk::guestList");
    }

    public function companies()
    {
        if(isset($_GET['company_name']))
        {
            $cps = \Kris\Frontdesk\Company::where('name', 'LIKE', '?')->setBindings(["%".$_GET['company_name']."%"])->get();

            return \View::make("Frontdesk::companyList",["companies"=>$cps]);
        }
        return view("Frontdesk::companyList");
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

    public function saveRefund()
    {

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
        $sql = "select room_number,type_name,floor_name,status_name,concat_ws(' ',firstname,lastname) as Guest,group_name,name,checkin,checkout,phone_ext,status_code,idrooms,idreservation,idroom_types,idfloors,group_concat(idreservation) as ids from rooms
                        join room_types on type_id = idroom_types
                        join floors on rooms.floors_id = idfloors
                        join room_status on status_code = rooms.status

                        left join reservations on date(checkin)<='".(\Kris\Frontdesk\Env::WD()->format('Y-m-d')). "' and  reservations.room_id= rooms.idrooms and checked_out is null and rooms.status in (" . \Kris\Frontdesk\RoomStatus::RESERVED.",".\Kris\Frontdesk\RoomStatus::OCCUPIED.") and reservations.status IN(" .\Kris\Frontdesk\Reservation::ACTIVE . ",".\Kris\Frontdesk\Reservation::CHECKEDIN .") ".
                        "left join guest on guest.id_guest = reservations.guest_id
                        left join companies on company_id = idcompanies
                        left join reservation_group on reservation_group.groupid = reservations.group_id
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

        left join reservations on date(checkin)<='".(\Kris\Frontdesk\Env::WD()->format('Y-m-d')). "' and (room_id= idrooms and checked_out is null) and reservations.status in(1,5)
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
        //\ORG\POS::Log("Access Booking View","default");

        return \View::make("Frontdesk::bookingView")->with(array('types'=>$data));
    }

    public function getBookingData()
    {
        $date = $_GET['startdate'];
        $days = $_GET['days'];
        $_date =  new \DateTime($date);
        $new_date = new \DateTime($date);
        $enddate = $new_date->add(new \DateInterval("P{$days}D"))->format("Y-m-d");

        $data = \DB::connection("mysql_book")->select("select concat_ws(' ',firstname,lastname)as guest,group_name,room_number,room_id,reservation_status.status_name,idreservation as reservation_id,greatest('{$_date->format("Y-m-d")}',
date_format(checkin,'%Y-%m-%d'))  as checkin,date_format(checkout,'%d_%m') as checkout,datediff(date(checkout),greatest('{$_date->format("Y-m-d")}',
date_format(checkin,'%Y-%m-%d')))-(1) as days,reservations.status as reservation_status from reservations
            join rooms on rooms.idrooms = room_id
join reservation_status on reservation_status.idreservation_status = reservations.status
left join guest on guest.id_guest = guest_id
left join reservation_group on reservation_group.groupid = reservations.group_id
            where reservations.status not in (2,3,4) and date(checkin) <= ? and date(checkout) >=?  and
datediff(date(checkout),greatest('{$_date->format("Y-m-d")}',date_format(checkin,'%Y-%m-%d')))>=0

order by reservations.status desc ",[$enddate,$date]);

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
        \FO::log("Created banquet booking");
        return redirect()->back()->with("msg","Order saved");
    }

    public function deleteBanquetOrder()
    {
        $id = $_GET['id'];
        return \Kris\Frontdesk\Banquet::deleteOrder($id);
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
                        \FO::log("Added laundry order".$res->idreservation);
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

    public function groupReservationListForm()
    {
        $d = \Kris\Frontdesk\Env::WD()->format("Y-m-d");
        $startdate = \Request::input("fromdate",$d);
        $enddate = \Request::input("todate",$d);

        $groups = \Kris\Frontdesk\ReservationGroup::whereBetween(\DB::raw("date(arrival)"),[$startdate,$enddate])->limit("40")->get();
        return \View::make("Frontdesk::groupList",["groups"=>$groups]);
    }

    public function groupViewer()
    {
        return \View::make("Frontdesk::groupViewer",["group"=>\Kris\Frontdesk\ReservationGroup::find($_GET['id'])]);
    }

    public function occupancyChart()
    {
        $room_chart  = \Kris\Frontdesk\Controllers\ReportsController::RoomStatusChartJson();
        return \View::make("Frontdesk::reports.OccupancyChart",["data"=>$room_chart]);
    }

    public function removePayment($id)
    {
        $payment = \Kris\Frontdesk\Payment::find($id);

        if($payment->reservation->status == \Kris\Frontdesk\Reservation::CHECKEDOUT || ((new \Carbon\Carbon($payment->date))->format("Y-m-d") != \Kris\Frontdesk\Env::WD()->format("Y-m-d"))) return redirect()->back()->withErrors("You can only delete payments made today and currently checkedin guest");

        $amount_credit = -$payment->credit;
        $amount_debit = +$payment->debit;
        $deleted  = $payment->delete();
        $updated = $payment->reservation->update(["paid_amount"=> $payment->reservation->paid_amount+$amount_credit+$amount_debit]);

        if($updated && $deleted) {
            \FO::log("Delete payment ".$id." of ".($amount_credit+$amount_debit));
            return redirect()->back()->with(['msg'=>"Payment Deleted"]);
        }else {
            redirect()->back()->withErrors("Payment Deletion failed");
        }

        return redirect()->back();
    }

    public function exRates()
    {
        if(\Request::isMethod("post"))
        {
            //Update
            $data = \Request::all();
            $keys = array_keys($data);
            try {
                for($i =0;$i<count($keys);$i++)
                {
                    if($i%2==0)
                    {
                        continue;
                    }

                    $cr = \Kris\Frontdesk\Currency::find(explode("_", $keys[$i])[1]);
                    $cr->alias = $data[$keys[$i]];
                    $cr->rate = $data[$keys[$i+1]];
                    $cr->save();
                }

                return redirect()->back()->with("msg","Rates saved successfully");
            }catch(\Exception $x)
            {
                return redirect()->back()->withErrors(["Error saving the rates"]);
            }
        }else {
            return \View::make("Frontdesk::exRates");
        }
    }

    function addCurrency()
    {
        if(\Request::isMethod("post"))
        {
            //save
            $currency= \Kris\Frontdesk\Currency::create([
                "name"=>\Request::input("name"),
                "alias"=>\Request::input("alias"),
                "rate"=>\Request::input("rate")
                ]);
            if($currency != null)
            {
                return redirect()->back()->with("msg","Currency Created");
            }else {
                return redirect()->back()->withErrors(["Currency Creation failed"]);
            }
        }else {
            return \View::make("Frontdesk::addCurrency");
        }

    }


    function printReceipt($id)
    {
        $pay = \Kris\Frontdesk\Payment::find($id);
        return \View::make("Frontdesk::printReceipt",["payment"=>$pay]);
    }


    /** Rooms Mananegment **/

    public function createRoomType()
    {
        if(\Request::isMethod("post"))
        {
            $data = \Request::all();

            if(strlen($data['name'])<2)
            {
                return redirect()->back()->withErrors(["Invalid Room Type name"]);
            }

            if(strlen($data['name'])<1)
            {
                return redirect()->back()->withErrors(["Alias Is Required"]);
            }

            \Kris\Frontdesk\RoomType::create([
                "type_name"=>$data["name"],
                "alias"=>$data["alias"],
                "date"=>\Kris\Frontdesk\Env::WD()->format("Y-m-d H:i:s")
                ]);

            return redirect()->back()->with("msg","Room Type created");
        }
        return \View::make("Frontdesk::settings.createRoomType");
    }


    public function createRoom()
    {
        if(\Request::isMethod("post"))
        {
            $data = \Request::all();

            if($data['type']<1)
            {
                return redirect()->back()->withErrors(["Please Choose Room Type"]);
            }

            if($data['floor']<1)
            {
                return redirect()->back()->withErrors(["Please Choose a floor"]);
            }

            if(strlen($data['name'])<2)
            {
                return redirect()->back()->withErrors(["Invalid Room Name"]);
            }

            \Kris\Frontdesk\Room::create([
                "room_number"=>$data["name"],
                "phone_ext"=>$data["phone"],
                "type_id"=>$data["type"],
                "floors_id"=>$data["floor"],
                "status"=>"1",
                "date"=>\Kris\Frontdesk\Env::WD()->format("Y-m-d H:i:s")
                ]);

            return redirect()->back()->with("msg","Room created");
        }
        return \View::make("Frontdesk::settings.createRoom");
    }

    public function setRoomRates()
    {
        if(\Request::isMethod("post"))
        {
            $data = \Request::all();

            if($data['type']<1)
            {
                return redirect()->back()->withErrors(["Please Choose Room Type"]);
            }

            if($data['rate_id']<1)
            {
                return redirect()->back()->withErrors(["Please Choose Rate Type"]);
            }

            if(!is_numeric($data['rate']))
            {
                return redirect()->back()->withErrors(["Invalid Rate amount"]);
            }


             \DB::connection(\Kris\Frontdesk\RoomRate::$_connection)->insert("insert into room_rates (rate_type_id,room_type_id,rate_amount) values(?,?,?) on duplicate key update rate_amount=?",
                [$data['rate_id'],$data['type'],$data['rate'],$data['rate']]);

            return redirect()->back()->with("msg","The Rate has been Set !");
        }
        return \View::make("Frontdesk::settings.setRoomRates");
    }

    public function addUser()
    {
        if(\Request::isMethod("post"))
        {
            $data = \Request::all();

            if(strlen($data['firstname'])<1)
            {
                return redirect()->back()->withErrors(["Please Choose Room Type"]);
            }


            if(strlen($data['password'])<6)
            {
                return redirect()->back()->withErrors(["Your Password is too short"]);
            }


            if($data['password'] != ($data['password2']))
            {
                return redirect()->back()->withErrors(["Your Passwords do not match"]);
            }


            if($data['role']<1)
            {
                return redirect()->back()->withErrors(["Please Choose a Role"]);
            }


            if(\Kris\Frontdesk\User::where("username",$data['username'])->get()->first())
            {
                return redirect()->back()->withErrors(["User already exist"]);
            }

            \Kris\Frontdesk\User::create([
                "username"=>$data['username'],
                "password"=>md5($data['password']),
                "firstname"=>$data['firstname'],
                "lastname"=>$data['lastname'],
                "is_active"=>"1",
                "group_id"=>$data['role'],
                "date"=>date("Y-m-d")
                ]);
            return redirect()->back()->with("msg","User Created !");
        }
        return \View::make("Frontdesk::settings.addUser");
    }

    public function userList()
    {
        return \View::make("Frontdesk::settings.userList");
    }
}
