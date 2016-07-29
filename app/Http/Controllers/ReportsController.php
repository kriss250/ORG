<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use \ORG;



class ReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    private $dateRange = array();
    
    public function index($report)
    {
        
        if(isset($_GET['date_range'])){
            
            $this->dateRange = explode('-' ,urldecode($_GET['date_range']));
            
            if(count($this->dateRange)==2){
                $this->dateRange[0] = \ORG\Dates::ToDBFormat( $this->dateRange[0]);
                $this->dateRange[1] = \ORG\Dates::ToDBFormat( $this->dateRange[1]);
            }else {
                $this->dateRange = array();
            }
        }
        
        switch($report)
        {
            case "CancelledResReport":
                return $this->CancelledReservations();
            case "ReservationReport" :
                return $this->Reservations();
            case "VoidReservationReport":
                return $this->VoidReservations();
            case "NoShowResReport":
                return $this->noShowReservations();
            case "GroupResReport":
                return $this->GroupReservation();
            case "MonthlyRoomIncome":
                return $this->MonthlyRoomIncome();
            case "MonthlyRoomSales" :
                return $this->MonthlyRoomSales();
            case "PaymentsStatements" :
                return $this->PaymentsStatements();
            case "VoidPayments" :
                return $this->VoidPaymentsStatements();
            case "Refunds" :
                return $this->Refunds();
            case "UnpaidBills":
                return $this->UnpaidBills();
            case "Credits" :
                return $this->Credits();
        }
        
        return "";
    }
    
    private function  MonthlyRoomIncome()
    {
        $data = DB::connection("mysql")->select("select room_number,room_types.type_name,datediff(checkout,checkin) as nights,format((night_rate*datediff(checkout,checkin)),0) as projected_income,format(sum(balance_amount),0) as Income from rooms 
            join room_types on idroom_types= rooms.type_id
            left join reservations on reserved_rooms.reservation_id = idreservation
              group by idrooms");
        $col_array = array(
                         array("data"=>"room_number","show"=>"Room"),
                         array("data"=>"type_name","show"=>"Room Type"),
                         array("data"=>"nights","show"=>"Nights"),
                         array("data"=>"projected_income","show"=>"Projected Income"),
                         array("data"=>"Income","show"=>"Income"),

             );
        $summary = "function ( row, data, start, end, display ) {
                     var api = this.api(), data;

            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            totalProj = api
                .column( 3 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                } );
            totalIncome =  api
                .column( 4 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                } );
                
            // Update footer
            $('.report_summary > .content').html(
            '<p>Projected Income : '+ accounting.formatMoney(totalProj)+'</p>'+
            '<p>Revenue :'+accounting.formatMoney(totalIncome)+'</p>'
            
            );

    },";

        return $this->Generate($col_array,$data,"Monthly Room Sales",$summary);
    }
    
    private function MonthlyRoomSales(){
        
        $data = DB::connection("mysql")->select("select room_number, group_concat(night_rate*datediff(checkout,checkin)) as Rates,group_concat(date_format(reservations.date,'%d')) as dayofmo from rooms 
                    left join reserved_rooms on room_id=idrooms
                    left join reservations  on idreservation = reservation_id
                    group by idrooms order by room_number asc
            ");
        return \View::make("/ORGFrontdesk/Reports/MonthlyRoomSales",["data"=>$data]);
        
    }
    
    private function GroupReservation()
    {
        $sql = "select count(reserved_rooms.reservation_id) as roomsNo,group_concat(room_number) as Rooms,group_concat(type_name) as Roomtypes, concat_ws(' ',firstname,lastname) As Guest,checkin,checkout,concat_ws(',',adults,children) as PAX,rate_types.name,night_rate,balance_amount,due_amount,reservations.date from reservations
                join reserved_rooms on reservation_id = idreservation
                join rooms on room_id = idrooms
                join room_types  on rooms.type_id = idroom_types
                join guest on guest.id_guest = guest_in
                join accounts on accounts.reservation_id = idreservation
                join rate_types on rate_id = idrate_types
                where reservations.status=".ORG\Reservation::ACTIVE. (!empty($this->dateRange) ? ' and reservations.date between (\''.$this->dateRange[0].'\') and (\''.$this->dateRange[1].'\') ' :''). "   group by reserved_rooms.reservation_id having count(reserved_rooms.reservation_id) > 1"
                ;
        
        $data = DB::connection("mysql")->select($sql);
        
        $col_array = array(
                        array("data"=>"Rooms"),
                        array("data"=>"Roomtypes","show"=>"Room Type"),
                        array("data"=>"roomsNo"),
                        array("data"=>"Guest"),
                        array("data"=>"checkin"),
                        array("data"=>"checkout"),
                        array("data"=>"PAX"),
                        array("data"=>"name","show"=>"Rate"),
                        array("data"=>"night_rate"),
                        array("data"=>"balance_amount","show"=>"Balance"),
                        array("data"=>"due_amount","show"=>"Due"),
                        array("data"=>"date"),
            );
        
        return $this->Generate($col_array,$data,"Group Reservations");
    }
    
    private function noShowReservations()
    {
        $data = DB::connection("mysql")->select("select room_number,type_name, concat_ws(' ',firstname,lastname) As Guest,checkin,checkout,concat_ws(',',adults,children) as PAX,rate_types.name,night_rate,reservations.date from reservations
                join reserved_rooms on reservation_id = idreservation
                join rooms on room_id = idrooms
                join room_types  on rooms.type_id = idroom_types
                join guest on guest.id_guest = guest_in
                join rate_types on rate_id = idrate_types
                where reservations.status=".ORG\Reservation::NOSHOW) ;

        $col_array = array(
                        array("data"=>"room_number","show"=>"Room"),
                        array("data"=>"type_name","show"=>"Room Type"),
                        array("data"=>"Guest"),
                        array("data"=>"checkin"),
                        array("data"=>"checkout"),
                        array("data"=>"PAX"),
                        array("data"=>"name","show"=>"Rate"),
                        array("data"=>"night_rate"),
                        array("data"=>"date"),
            );
        
        return $this->Generate($col_array,$data,"No Show Reservations");
    }
    
    private function VoidReservations()
    {
        $data = DB::connection("mysql")->select("select room_number,type_name, concat_ws(' ',firstname,lastname) As Guest,checkin,checkout,concat_ws(',',adults,children) as PAX,rate_types.name,night_rate,reservations.date from reservations
                join reserved_rooms on reservation_id = idreservation
                join rooms on room_id = idrooms
                join room_types  on rooms.type_id = idroom_types
                join guest on guest.id_guest = guest_in
                join rate_types on rate_id = idrate_types
                where reservations.status=".ORG\Reservation::VOID) ;

        $col_array = array(
                        array("data"=>"room_number","show"=>"Room"),
                        array("data"=>"type_name","show"=>"Room Type"),
                        array("data"=>"Guest"),
                        array("data"=>"checkin"),
                        array("data"=>"checkout"),
                        array("data"=>"PAX"),
                        array("data"=>"name","show"=>"Rate"),
                        array("data"=>"night_rate"),
                        array("data"=>"date"),
            );
        
        return $this->Generate($col_array,$data,"Void Reservations");
    }
    
    private function CancelledReservations()
    {
        $data = DB::connection("mysql")->select("select room_number,type_name, concat_ws(' ',firstname,lastname) As Guest,checkin,checkout,concat_ws(',',adults,children) as PAX,rate_types.name,night_rate,reservations.date from reservations
                join reserved_rooms on reservation_id = idreservation
                join rooms on room_id = idrooms
                join room_types  on rooms.type_id = idroom_types
                join guest on guest.id_guest = guest_in
                join rate_types on rate_id = idrate_types
                where reservations.status=".ORG\Reservation::CANCELLED);

        $col_array = array(
                        array("data"=>"room_number","show"=>"Room"),
                        array("data"=>"type_name","show"=>"Room Type"),
                        array("data"=>"Guest"),
                        array("data"=>"checkin"),
                        array("data"=>"checkout"),
                        array("data"=>"PAX"),
                        array("data"=>"name","show"=>"Rate"),
                        array("data"=>"night_rate"),
                        array("data"=>"date"),
            );
        
        return $this->Generate($col_array,$data,"Cancelled Reservations");
    }
    
    private function Reservations()
    {
        $data = DB::connection("mysql")->select("select reservations.status,status_name,room_number,type_name, concat_ws(' ',guest.firstname,guest.lastname) As Guest,checkin,checkout,concat_ws(',',adults,children) as PAX,rate_types.name,format(night_rate,0) as Rate,format(balance_amount,0) as balanceamount,format(due_amount,0) as dueamount,username,reservations.date from reservations
                join reserved_rooms on reservation_id = idreservation
                join rooms on room_id = idrooms
                join room_types  on rooms.type_id = idroom_types
                join guest on guest.id_guest = guest_in
                join accounts on accounts.reservation_id = idreservation
                join rate_types on rate_id = idrate_types
                join reservation_status on idreservation_status=reservations.status
                join users on reservations.user_id = idusers
                order by reservations.status");

        $col_array = array(
            array("data"=>"status","show"=>""),
                        array("data"=>"room_number","show"=>"Room"),
                        array("data"=>"type_name","show"=>"Room Type"),
                        array("data"=>"Guest","show"=>"Guest Name"),
                        array("data"=>"checkin"),
                        array("data"=>"checkout"),
                        array("data"=>"PAX","show"=>"Pax(a/c)"),
                        array("data"=>"name","show"=>"Rate"),
                        array("data"=>"Rate"),
                        array("data"=>"balanceamount","show"=>"Balance"),
                        array("data"=>"dueamount","show"=>"Due"),
                        array("data"=>"username","show"=>"User"),
                        array("data"=>"status_name","show"=>"Status"),
                        array("data"=>"date"),
            );
        
        return $this->Generate($col_array,$data,"Reservations");
    }
    
    public function Generate($cols,$data,$header,$footer=null)
    {
        $language=  array("decimal"=>".","thousands"=>",");
        $obj = array("iDisplayLength"=> "50","language"=>$language,"columns"=>$cols,"data"=>$data,"footerCallback"=>"footerPlaceHolder");
        
        $json = json_encode($obj);
        
        
        return \View::make("/ORGFrontdesk/Reports/Generator",["json_data"=>$json,"cols"=>$cols,"footer"=>$footer,"header"=>$header]);
    }
    
    public function PaymentsStatements()
    {
        $data = DB::connection("mysql")->select("select concat_ws(' ',guest.firstname,guest.lastname) as GuestName,credit,debit,method_name,motif,username,folio.date from folio
                    join users on idusers = folio.user_id
                    join reservations on idreservation = reservation_id
                    join pay_method on paymethod = idpay_method
                    join guest on id_guest = reservations.guest_id where debit=0 and folio.void=0 and folio.date = '".\ORG\Dates::WORKINGDATE(true)."'");

        $col_array = array(
                        array("data"=>"GuestName","show"=>"Guest"),
                        array("data"=>"credit","show"=>"Amount"),
                        array("data"=>"method_name","show"=>"Method of Pay."),
                        array("data"=>"username","show"=>"Added by"),
                        array("data"=>"motif"),
                        array("data"=>"date","show"=>"Date"),
            );
        
        return $this->Generate($col_array,$data,"Payments");
    }

    public function VoidPaymentsStatements()
    {
        $data = DB::connection("mysql")->select("select concat_ws(' ',guest.firstname,guest.lastname) as GuestName,credit,debit,method_name,motif,username,folio.date from folio
                    join users on idusers = folio.user_id
                    join reservations on idreservation = reservation_id
                    join pay_method on paymethod = idpay_method
                    join guest on id_guest = reservations.guest_id where debit = 0 and folio.void=0 and folio.date = '".\ORG\Dates::WORKINGDATE(true)."'");

        $col_array = array(
                        array("data"=>"GuestName","show"=>"Guest"),
                        array("data"=>"credit","show"=>"Amount"),
                        array("data"=>"method_name","show"=>"Method of Pay."),
                        array("data"=>"username","show"=>"Added by"),
                        array("data"=>"motif"),
                        array("data"=>"date","show"=>"Date"),
            );
        
        return $this->Generate($col_array,$data,"Payments");
    }

    public function Refunds(){
        $data = DB::connection("mysql")->select("select concat_ws(' ',guest.firstname,guest.lastname) as GuestName,credit,debit,method_name,motif,username,folio.date from folio
                    join users on idusers = folio.user_id
                    join reservations on idreservation = reservation_id
                    join pay_method on paymethod = idpay_method
                    join guest on id_guest = reservations.guest_id where credit=0 and folio.void=0 and folio.date = '".\ORG\Dates::WORKINGDATE(true)."'");

        $col_array = array(
                        array("data"=>"GuestName","show"=>"Guest"),
                        array("data"=>"credit","show"=>"Amount"),
                        array("data"=>"method_name","show"=>"Method of Pay."),
                        array("data"=>"username","show"=>"Added by"),
                        array("data"=>"motif"),
                        array("data"=>"date","show"=>"Date"),
            );
        
        

        
        return $this->Generate($col_array,$data,"Refunds");
    }
    
    public function UnpaidBills()
    {
        return "SDS";
    }
    public function Credits(){
        $data = DB::connection("mysql")->select("SELECT concat_ws(' ',guest.firstname,guest.lastname) as Guest,amount,paid_amount,username,folio.date FROM credits
                join reservations on idreservation = credits.reservation_id
                join guest on reservations.guest_id = id_guest
                join folio on folio.id_folio = credits.folio_id
                join users on idusers = folio.user_id");

        $col_array = array(
                        array("data"=>"Guest"),
                        array("data"=>"amount","show"=>"Amount"),
                        array("data"=>"paid_amount","show"=>"Amount Paid"),
                        array("data"=>"username","show"=>"Added by"),
                        array("data"=>"date","show"=>"Date"),
            );
        
        $summary = "function ( row, data, start, end, display ) {
                     var api = this.api(), data;

            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            totalCr= api
                .column( 1 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                } );
            totalPaidCr =  api
                .column( 2 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                } );
                
            // Update footer
            $('.report_summary > .content').html(
            '<p>Total Credits : '+ accounting.formatMoney(totalCr)+'</p>'+
            '<p>Paid Credits :'+accounting.formatMoney(totalPaidCr)+'</p>'
            
            );

    },";
        
        return $this->Generate($col_array,$data,"Refunds",$summary);
    }
}
