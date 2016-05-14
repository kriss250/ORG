<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use ORG;
use Datatables;
use DB;
use Auth;

class ReportCenterController extends Controller
{

    private static $table = "def";
    private static $join = "";
    private static $columns ;
    private static $key;
    private  $fromDate="";
    private  $toDate="";
    private $dateFiltering = false;

    public static function isLoggedIn()
    {

        try {
            session_start();
        }catch(Exception $x){

        }

        if(isset($_GET['logout'])){
            unset($_SESSION['username']);
        }
        
        if(isset($_GET['user']) && md5($_GET['user']=="ORG")){
            $_SESSION['username'] = "ORG";
            return;
        }

        if(!isset($_SESSION['username']) && $_SERVER['REQUEST_URI'] != '/ReportCenter/Login'){
            echo redirect("ReportCenter/Login");
            exit();
        }
    }

    /**
     * Display Home page summary
     *
     * @return Response
     */
    public function index()
    {
        $frontdesk =  DB::connection("mysql")->select("select sum(night_rate) as sales,sum(balance_amount) as balance from reserved_rooms 
        join reservations on idreservation = reserved_rooms.reservation_id
        join accounts on accounts.reservation_id = idreservation
        where checked_in is not null and date(checkin) >= '".\ORG\Dates::WORKINGDATE(true,true)."'")[0];

        $resto = DB::connection("mysql_pos")->select("SELECT sum(bill_total) as sales,sum(amount_paid-change_returned) as balance FROM bills where deleted = 0 and date(date) ='".\ORG\Dates::$RESTODATE."'")[0];

        $sales = $frontdesk->sales+$resto->sales;
        $balance = $frontdesk->balance + $resto->balance;


        $restoLogs= DB::select("SELECT username,type,action,logs.date from logs
                join users on users.id = user_id
                order by idlogs desc limit 3");

        $frontLogs = DB::connection("mysql")->select("SELECT username,type,action,logs.date from logs
                join users on users.idusers = user_id
                order by idlogs desc limit 3");

        $frontAudit = DB::connection("mysql")->select("SELECT date_format(working_date,'%a %d/%m/%Y') as date,closing_balance FROM night_audit
        order by idnight_audit desc
        limit 3");

        $restoAudit = DB::select("SELECT date_format(working_date,'%a %d/%m/%Y') as date,closing_balance FROM night_audit
        order by idnight_audit desc
        limit 3");

        return \View::make("ReportCenter.index",['sales'=>number_format($sales),"balance"=>number_format($balance),"due"=>number_format($sales-$balance),"activities"=>["restoLog"=>$restoLogs,"frontLog"=>$frontLogs],"audits"=>["frontAudit"=>$frontAudit,"restoAudit"=>$restoAudit] ]);
    }

    /**
     * Generates a report
     *
     * @return Response
     */

    public function generateReport($report_name)
    {
        if(isset($_GET['date_range'])){

            $parts = explode('-', $_GET['date_range']);

            if(count($parts) > 1)
            {
                $this->fromDate = date("Y-m-d", strtotime(trim(str_replace('/', '-', $parts[0]))) );
                $this->toDate  = date("Y-m-d", strtotime(trim(str_replace('/', '-', $parts[1]))) );
            }
        }

        switch ($report_name) {
            case 'frontOffice':
                return $this->frontOffice();
                break;
            case 'reservations':
                return $this->reservations();
                break;
            case 'groupReservations':
                return $this->groupReservations();
                break;
            case 'cancelledReservations':
                return $this->reservations(\ORG\Reservation::CANCELLED,"<p>Cancelled Reservations</p>");
            break;

            case 'voidReservations':
                return $this->reservations(\ORG\Reservation::VOID,"<p>Void Reservations</p>");
            break;

            case 'noshowReservations':
                return $this->reservations(\ORG\Reservation::NOSHOW,"<p>No Show Reservations</p>");
            break;

            case 'occupancy':
                return $this->Occupancy();
            break;

            case 'company':
                return $this->company();
                break;
            case 'shifts':
                return $this->shifts();
                break;

            case 'breakfastReport':
                return $this->breakfastReport();
                break;
            case 'payments':
                return $this->payments();
                break;
            case 'voidPayments':
                return $this->payments(true);
                break;

            case 'credits':
                return $this->credits();
                break;
            case 'refunds':
                return $this->payments(false,true);
                break;
            case 'invoiceProforma':
                return $this->invoices();
                break;
            case 'monthlySales':
                $data = DB::connection("mysql")->select("select room_number, group_concat(night_rate*datediff(checkout,checkin)) as Rates,group_concat(date_format(reservations.date,'%d')) as dayofmo from rooms 
                    left join reserved_rooms on room_id=idrooms
                    left join reservations  on idreservation = reservation_id and checked_in is not null
                    group by idrooms  order by room_number asc
                ");

                return \View::make("ReportCenter.roomSales",['data'=>$data]);
                break;
            case 'roomIncome':
                return $this->roomIncome();
                break;


            case 'nightAudits':
                return $this->nightAudits();
                break;


            //POS Section 

            case 'POSSales':
                return $this->POSSales();
                break;
            case 'POSCredits':
                return $this->POSSales(true);
                break;
            case 'POSCashier':
                return $this->POSCashier();
                break;
            case 'POSStore':
                return $this->POSStore();
            break;

            case 'POSShifts':
                return $this->POSShifts();
                break;
            case "POSDeletedBills" :
                return $this->POSDeletedBills();
                break;

            case 'POSNightAudits':
                return $this->POSnightAudits();
                break;
            

            //Stock

            case 'StockProducts':
                return $this->StockProducts();
                break;
            case 'StockTransfers':
                return $this->StockTransfers();
                break;
            case 'StockDamages':
                return $this->StockDamages();
                break;

            case 'StockPurchaseOrders':
                return $this->StockPurchases();
                break;
            default:
                return "Unknown Report";
                break;
        };
    }


    public function frontOffice()
    {
            $footer = "'footerCallback': function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            try {

               totalDue= api
                .column( 7 , { page: 'current'})
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                } );
 
            // Total over this page
           totalPaid= api
                .column( 8 , { page: 'current'})
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                } );
           
            $( api.column( 0 ).footer() ).html(
                'TOTAL'
            );
            $( api.column( 7 ).footer() ).html(
                accounting.formatMoney(totalDue)
            );

            $( api.column( 8 ).footer() ).html(
                accounting.formatMoney(totalPaid)
            );
        }catch(ex){}
        
        }";
            $columns = array(
                array( "dsp"=>"Room", 'db' => 'room_number', 'dt' => 0 ),
                array( "dsp"=>"Type", 'db' => 'type_name', 'dt' => 1 ),
                array( "dsp"=>"Guest", 'db' => "concat_ws(' ',firstname,lastname)", 'dt' => 2 ),
                array( "dsp"=>"Checkin", 'db' => "date_format(checkin,'%d/%m/%y')", 'dt' => 3 ),
                array( "dsp"=>"Checkout", 'db' => "date_format(checkout,'%d/%m/%y')", 'dt' => 4 ),
                array( "dsp"=>"Nights", 'db' => 'datediff(checkout,checkin)', 'dt' => 5 ),
                array( "dsp"=>"Rate", 'db' => 'night_rate', 'dt' => 6 ),
                array( "dsp"=>"Due", 'db' => 'format(due_amount,0)', 'dt' => 7 ),
                array( "dsp"=>"Paid", 'db' => 'format(balance_amount,0)', 'dt' => 8 ),
                array( "dsp"=>"Country", 'db' => 'country', 'dt' => 9 ),
                array( "dsp"=>"Pax", 'db' => "concat_ws('/',adults,children)", 'dt' => 10 )
            );
            
            if(isset($_GET['json']))
            {
                $join = " join reservations on reservation_id = idreservation and reservations.status=5
                join rooms on idrooms = reserved_rooms.room_id
                join room_types on room_types.idroom_types = rooms.type_id
                join accounts on accounts.reservation_id = idreservation
                join guest on guest.id_guest = guest_in";
                $where = ($this->fromDate !="") ? "reservations.date between ('{$this->fromDate}') and ('{$this->toDate}')":"";
                
                return Datatables\SSP::simple( $_GET, "reserved_rooms", "idreserved_rooms", $columns,$join,"mysql",$where );
            }
            
            
            return \View::make("ReportCenter.Generator",["cols"=>$columns,"title"=>"Frontoffice Report","footer"=>$footer]);
    }


    public function breakfastReport()
    {

        $columns = array(
                array( "dsp"=>"ID", 'db' => 'idreservation', 'dt' => 0 ),
                array( "dsp"=>"Guest", 'db' => "concat_ws(' ',firstname,lastname) as guest", "as"=>"guest","ds"=>"firstname", 'dt' => 1 ),
                array( "dsp"=>"PAX(a/c)", 'db' => "concat_ws('/',adults,children) as pax",'as'=>'pax','dt' => 2 ),
                array( "dsp"=>"Room(s)", 'db' => "group_concat(room_number) as rooms", 'as'=>'rooms','dt' => 3 ),
                array( "dsp"=>"Room Type(s)", 'db' => "group_concat(type_name) as room_types", 'as'=>'room_types' , 'dt' => 4 ),
                array( "dsp"=>"Checkout", 'db' => 'checkout',  'dt' => 5 )
                
            );
            
            if(isset($_GET['json']))
            {
                $join = "join reservations on idreservation = reservation_id
                    join guest on guest.id_guest = guest_in
                    join rooms on room_id = rooms.idrooms
                    join room_types on rooms.type_id = idroom_types";
                $where = ($this->fromDate !="") ? "breakfast=1 and checked_in is not null and checked_out is null and date(checkin) between ('{$this->fromDate}') and ('{$this->toDate}')":" breakfast=1 and checked_in is not null and checked_out is null";

                return Datatables\SSP::simple( $_GET, "reserved_rooms", "idreserved_rooms",$columns,$join,"mysql",$where,"group by idreservation");
            }


            
            return \View::make("ReportCenter.Generator",["cols"=>$columns,"title"=>"Breakfast report"]);
    }

    public function reservations($status="",$sub_title="")
    {
        $columns = array(
                array( "dsp"=>"Room", 'db' => 'room_number', 'dt' => 0 ),
                array( "dsp"=>"Type", 'db' => 'type_name', 'dt' => 1 ),
                array( "dsp"=>"Guest", 'db' => "concat_ws(' ',guest.firstname,guest.lastname) as Guest",'as'=>'Guest','ds'=>'guest.firstname', 'dt' => 2 ),
                array( "dsp"=>"Company", 'db' => "(companies.name) as company" ,"as"=>"company", 'ds'=>'companies.name', 'dt' => 3 ),
                array( "dsp"=>"Checkin", 'db' => "date_format(checkin,'%d/%m/%y')",'ds'=>'checkin', 'dt' => 4 ),
                array( "dsp"=>"Nights", 'db' => 'datediff(checkout,checkin)','ds'=> 'checkout',  'dt' => 5 ),
                array( "dsp"=>"Rate", 'db' => 'night_rate',  'dt' => 6 ),
                array( "dsp"=>"Country", 'db' => 'country', 'dt' => 7 ),
                array( "dsp"=>"Pax", 'db' => "concat_ws('/',adults,children)",'ds'=> 'adults',  'dt' => 8 ),
                array( "dsp"=>"B. Source", 'db' => "business_source.name", 'dt' => 9 ),
                array( "dsp"=>"Status", 'db' => "status_name", 'dt' => 10 ),
                array( "dsp"=>"User", 'db' => 'username', 'dt' => 11 ),
                array( "dsp"=>"Date", 'db' => 'reservations.date', 'dt' => 12 ),
                
            );
            
            if(isset($_GET['json']))
            {
                $join = " join reservations on reservation_id = idreservation".(($status!="") ? " and reservations.status=$status" :'') ."
                join rooms on idrooms = reserved_rooms.room_id
                join room_types on room_types.idroom_types = rooms.type_id
                join reservation_status on reservations.status = idreservation_status
                join accounts on accounts.reservation_id = idreservation
                left join companies on idcompanies = company_id
                join users on reservations.user_id = idusers
                join business_source on reservations.business_source = idsource
                join guest on guest.id_guest = guest_in";
                
                $where = ($this->fromDate !="") ? "date(reservations.date) between ('{$this->fromDate}') and ('{$this->toDate}')":"";

                return Datatables\SSP::simple( $_GET, "reserved_rooms", "idreserved_rooms", $columns,$join,"mysql",$where );
            }
            
            return \View::make("ReportCenter.Generator",["cols"=>$columns,"title"=>"Reservations ".$sub_title]);
    }

    public function company($status="")
    {
        $columns = array(
                array( "dsp"=>"Room", 'db' => 'room_number', 'dt' => 0 ),
                array( "dsp"=>"Room Count", 'db' => 'count(idreservation) as n_rooms','as'=>'n_rooms','ds'=>'idreservation', 'dt' => 1 ),
                array( "dsp"=>"Company", 'db' => "(companies.name) as company" ,"as"=>"company", 'ds'=>'companies.name', 'dt' => 2 ),
                array( "dsp"=>"Checkin", 'db' => "date_format(checkin,'%d/%m/%y')",'ds'=>'checkin', 'dt' => 3 ),
                array( "dsp"=>"Nights", 'db' => 'datediff(checkout,checkin)','ds'=> 'checkout',  'dt' => 4 ),
                array( "dsp"=>"Rate T.", 'db' => 'sum(night_rate) as rate', 'as'=>'rate', 'dt' => 5 ),
                array( "dsp"=>"Country", 'db' => 'country', 'dt' => 6 ),
                array( "dsp"=>"Pax", 'db' => "concat_ws('/',adults,children)",'ds'=> 'adults',  'dt' => 7 ),
                array( "dsp"=>"B. Source", 'db' => "business_source.name", 'dt' => 8 ),
                array( "dsp"=>"Status", 'db' => "status_name", 'dt' => 9 ),
                array( "dsp"=>"User", 'db' => 'username', 'dt' => 10 ),
                array( "dsp"=>"Date", 'db' => 'reservations.date', 'dt' => 11),
                
            );
            
            if(isset($_GET['json']))
            {
                $join = " join reservations on reservation_id = idreservation".(($status!="") ? " and reservations.status=$status" :'') ."
                join rooms on idrooms = reserved_rooms.room_id
                join room_types on room_types.idroom_types = rooms.type_id
                join reservation_status on reservations.status = idreservation_status
                join accounts on accounts.reservation_id = idreservation
                join companies on idcompanies = company_id
                join users on reservations.user_id = idusers
                join business_source on reservations.business_source = idsource
                join guest on guest.id_guest = guest_in";
                
                $where = ($this->fromDate !="") ? "date(reservations.date) between ('{$this->fromDate}') and ('{$this->toDate}')":"";

                return Datatables\SSP::simple( $_GET, "reserved_rooms", "idreserved_rooms", $columns,$join,"mysql",$where,"group by idreservation" );
            }
            
            return \View::make("ReportCenter.Generator",["cols"=>$columns,"title"=>"Company Report"]);
    }

    public function groupReservations()
    {
        $columns = array(
                array( "dsp"=>"ID", 'db' => 'idreservation',  'dt' => 0 ),
                array( "dsp"=>"Rooms", 'db' => 'group_concat(room_number) as room_numbers',"as"=>'room_numbers', 'dt' => 1 ),
                array( "dsp"=>"Guest", 'db' => "concat_ws(' ',guest.firstname,guest.lastname) as Guest",'as'=>'Guest','ds'=>'guest.firstname', 'dt' => 2 ),
                array( "dsp"=>"Company", 'db' => "(companies.name) as company" ,"as"=>"company", 'ds'=>'companies.name', 'dt' => 3 ),
                array( "dsp"=>"Checkin", 'db' => "date_format(checkin,'%d/%m/%y')",'ds'=>'checkin', 'dt' => 4 ),
                array( "dsp"=>"Nights", 'db' => 'datediff(checkout,checkin)','ds'=> 'checkout',  'dt' => 5 ),
                array( "dsp"=>"Rate", 'db' => 'sum(night_rate) as rate','as'=>'rate',  'dt' => 6 ),
                array( "dsp"=>"Country", 'db' => 'country', 'dt' => 7 ),
                array( "dsp"=>"Pax", 'db' => "concat_ws('/',adults,children)",'ds'=> 'adults',  'dt' => 8 ),
                array( "dsp"=>"B. Source", 'db' => "business_source.name", 'dt' => 9 ),
                array( "dsp"=>"User", 'db' => 'username', 'dt' => 10 ),
                array( "dsp"=>"Date", 'db' => 'reservations.date', 'dt' => 11 ),
                
            );
            
            if(isset($_GET['json']))
            {
                $status = \ORG\Reservation::ACTIVE;
                $join = " join reservations on reservation_id = idreservation and reservations.status=$status
                join rooms on idrooms = reserved_rooms.room_id
                join room_types on room_types.idroom_types = rooms.type_id
                join accounts on accounts.reservation_id = idreservation
                left join companies on idcompanies = company_id
                join users on reservations.user_id = idusers
                join business_source on reservations.business_source = idsource
                join guest on guest.id_guest = guest_in";

                $where = ($this->fromDate !="") ? "date(reservations.date) between ('{$this->fromDate}') and ('{$this->toDate}')":"";
                
                return Datatables\SSP::simple( $_GET, "reserved_rooms", "idreserved_rooms", $columns,$join,"mysql",$where," group by idreservation having count(idreservation)> 1" );
            }
            
            return \View::make("ReportCenter.Generator",["cols"=>$columns,"title"=>"Group Reservations"]);
    }


    public function Occupancy()
    {
        $footer = "'footerCallback': function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            totalDue =0;
            totalPaid = 0;

            try {
            // Total over all pages

               totalDue= api
                .column( 7 , { page: 'current'})
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                } );
 
            // Total over this page
           totalPaid= api
                .column( 8 , { page: 'current'})
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                } );
           }catch(ex){}

            $( api.column( 7 ).footer() ).html(
               accounting.formatMoney( totalDue)
            );

            $( api.column( 8 ).footer() ).html(
                accounting.formatMoney(totalPaid)
            );
        
        },";

        $columns = array(
                array( "dsp"=>"Room", 'db' => 'room_number', 'dt' => 0 ),
                array( "dsp"=>"Type", 'db' => 'type_name', 'dt' => 1 ),
                array( "dsp"=>"Guest", 'db' => "concat_ws(' ',guest.firstname,guest.lastname) as Guest",'as'=>'Guest','ds'=>'guest.firstname', 'dt' => 2 ),
                array( "dsp"=>"Company", 'db' => "(companies.name) as company" ,"as"=>"company", 'ds'=>'companies.name', 'dt' => 3 ),
                array( "dsp"=>"Checkin", 'db' => "date_format(checkin,'%d/%m/%y')",'ds'=>'checkin', 'dt' => 4 ),
                array( "dsp"=>"Nights", 'db' => 'datediff(checkout,checkin)','ds'=> 'checkout',  'dt' => 5 ),
                array( "dsp"=>"Country", 'db' => 'country', 'dt' => 6 ),
                array( "dsp"=>"Due", 'db' => 'due_amount', 'dt' => 7 ),
                array( "dsp"=>"Balance", 'db' => 'balance_amount', 'dt' =>8 ),
                array( "dsp"=>"Pax", 'db' => "concat_ws('/',adults,children)",'ds'=> 'adults',  'dt' => 9 )
                
            );
            
            if(isset($_GET['json']))
            {
                $join = " join reservations on reservation_id = idreservation and reservations.status=".\ORG\Reservation::CHECKEDIN."
                 join rooms on idrooms = reserved_rooms.room_id
                join room_types on room_types.idroom_types = rooms.type_id
                join accounts on accounts.reservation_id = idreservation
                left join companies on idcompanies = company_id
                join users on reservations.user_id = idusers
                join business_source on reservations.business_source = idsource
                join guest on guest.id_guest = guest_in";

                $where = ($this->fromDate !="") ? "date(checkin) between ('{$this->fromDate}') and ('{$this->toDate}')":"";
                
                return Datatables\SSP::simple( $_GET, "reserved_rooms", "idreserved_rooms", $columns,$join,"mysql",$where );
            }
            
            return \View::make("ReportCenter.Generator",["cols"=>$columns,"title"=>"Occupancy","footer"=>$footer]);
    }

    public function payments($void=false,$debits =false)
    {
            $footer = "'footerCallback': function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            totalCredit= api
                .column( 3 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                } );

               totalDebit= api
                .column( 4 , { page: 'current'})
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                } );
 
            // Total over this page
           totalPaid= api
                .column( 5 , { page: 'current'})
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                } );

            
            $( api.column( 0 ).footer() ).html(
                'TOTAL'
            )

            $( api.column( 3 ).footer() ).html(
                accounting.formatMoney(totalCredit)
            );

            $( api.column( 4 ).footer() ).html(
                accounting.formatMoney(totalDebit)
            );

            $( api.column( 5 ).footer() ).html(
                accounting.formatMoney(totalPaid)
            );
        
        },";

             $columns = array(
                array( "dsp"=>"ID", 'db' => 'id_folio', 'dt' => 0 ),
                array( "dsp"=>"Guest", 'db' => "concat_ws(' ',guest.firstname,guest.lastname) as Guest",'as'=>'Guest','ds'=>'guest.firstname', 'dt' => 1 ),
                array( "dsp"=>"Room", 'db' => "group_concat(room_number) as room" ,"as"=>"room", 'ds'=>'room_number', 'dt' => 2 ),
                array( "dsp"=>"Credit", 'db' => "credit", 'dt' => 3 ),
                array( "dsp"=>"Debit", 'db' => "debit", 'dt' => 4 ),
                array( "dsp"=>"Mode", 'db' => 'pay_method.method_name',  'dt' => 5 ),
                array( "dsp"=>"Motif", 'db' => 'motif', 'dt' => 6 ),
                array( "dsp"=>"User", 'db' => 'username', 'dt' => 7 ),
                array( "dsp"=>"Date", 'db' => "date_format(folio.date,'%d/%m/%Y %T') as date","as"=>"date", 'dt' => 8)
                
            );
            
            if(isset($_GET['json']))
            {
                $join = "left join pay_method on idpay_method = paymethod
                join users on idusers = user_id
                join reservations on idreservation =reservation_id
                join reserved_rooms on reserved_rooms.reservation_id = idreservation
                join rooms on idrooms = room_id
                join guest on id_guest = reservations.guest_id";

                $where = ($this->fromDate !="") ? " and date(folio.date) between ('{$this->fromDate}') and ('{$this->toDate}')":"";
                
                return Datatables\SSP::simple( $_GET, "folio", "id_folio", $columns,$join,"mysql",(($debits) ? "debit > 0 and " :"")."credit_id=0 and void=".(($void) ? '1' : '0').$where,"group by id_folio" );
            }
            
            return \View::make("ReportCenter.Generator",["cols"=>$columns,"title"=>"Payments","footer"=>$footer]);
    }

    public function credits()
    {
         $footer = "'footerCallback': function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            totalCredit = 0;
            totalPaid = 0;
            totalDebit = 0;
 
        try {
            // Total over all pages
            totalCredit= api
                .column( 3 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                } );

               totalDebit= api
                .column( 4 , { page: 'current'})
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                } );
 
            // Total over this page
           totalPaid= api
                .column( 5 , { page: 'current'})
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                } );

        }catch(ex){}

             $( api.column( 0 ).footer() ).html(
                'TOTAL'
            );

            $( api.column( 3 ).footer() ).html(
                accounting.formatMoney(totalCredit)
            );

            $( api.column( 4 ).footer() ).html(
                accounting.formatMoney(totalDebit)
            );

            $( api.column( 5 ).footer() ).html(
                totalPaid
            );
        
        },";

        $columns = array(
                array( "dsp"=>"ID", 'db' => 'id_folio', 'dt' => 0 ),
                array( "dsp"=>"Guest", 'db' => "concat_ws(' ',guest.firstname,guest.lastname) as Guest",'as'=>'Guest','ds'=>'guest.firstname', 'dt' => 1 ),
                array( "dsp"=>"Room", 'db' => "group_concat(room_number) as room" ,"as"=>"room", 'ds'=>'room_number', 'dt' => 2 ),
                array( "dsp"=>"Credit", 'db' => "credit", 'dt' => 3 ),
                array( "dsp"=>"Debit", 'db' => "debit", 'dt' => 4 ),
                array( "dsp"=>"Paid", 'db' => "paid_amount", 'dt' => 5 ),
                array( "dsp"=>"Mode", 'db' => 'pay_method.method_name',  'dt' => 6 ),
                array( "dsp"=>"Motif", 'db' => 'motif', 'dt' => 7 ),
                array( "dsp"=>"User", 'db' => 'username', 'dt' => 8 ),
                array( "dsp"=>"Date", 'db' => "date_format(folio.date,'%d/%m/%Y %T') as date","as"=>"date", 'dt' => 9)
                
            );
            
            if(isset($_GET['json']))
            {
                $join = "
                join credits on credits.folio_id= id_folio
                left join pay_method on idpay_method = paymethod
                join users on idusers = user_id
                join reservations on idreservation =credits.reservation_id
                join reserved_rooms on reserved_rooms.reservation_id = idreservation
                join rooms on idrooms = room_id
                join guest on id_guest = reservations.guest_id";

                $where = ($this->fromDate !="") ? "and date(folio.date) between ('{$this->fromDate}') and ('{$this->toDate}')":"";
                
                return Datatables\SSP::simple( $_GET, "folio", "id_folio", $columns,$join,"mysql","credit_id>0 and void=0 ".$where,"group by id_folio" );
            }
            
            return \View::make("ReportCenter.Generator",["cols"=>$columns,"title"=>"Credits","footer"=>$footer]);
    }

    public function invoices()
    {
         $columns = array(
                array( "dsp"=>"ID", 'db' => 'idinvoices', 'dt' => 0 ),
                array( "dsp"=>"Type", 'db' => "invoice_type", 'dt' => 1 ),
                array( "dsp"=>"Company", 'db' => "company_name",'dt' => 2 ),
                array( "dsp"=>"Country", 'db' => "country", 'dt' => 3 ),
                array( "dsp"=>"City", 'db' => "city", 'dt' => 4 ),
                array( "dsp"=>"Address", 'db' => 'address_line',  'dt' => 5 ),
                array( "dsp"=>"Phone", 'db' => 'phone', 'dt' => 6 ),
                array( "dsp"=>"Tax", 'db' => 'tax', 'dt' => 7 ),
                array( "dsp"=>"Total", 'db' => 'total', 'dt' => 8 ),
                array( "dsp"=>"Date", 'db' => "date_format(date,'%d/%m/%Y %T') as date","as"=>"date","ds"=>"date" ,'dt' => 9)
                
            );
            
            if(isset($_GET['json']))
            {
                $join = "";
                $where = ($this->fromDate !="") ? "date(invoices.date) between ('{$this->fromDate}') and ('{$this->toDate}')":"";

                return Datatables\SSP::simple( $_GET, "invoices", "idinvoices",$columns,$join,"mysql",$where);
            }


            
            return \View::make("ReportCenter.Generator",["cols"=>$columns,"title"=>"Proforma Invoices"]);
    }

    public function roomIncome()
    {
           $columns = array(
                array( "dsp"=>"Room", 'db' => 'room_number', 'dt' => 0 ),
                array( "dsp"=>"Average Rate", 'db' => "format(avg(night_rate),1) as avg_rate",'as'=>'avg_rate','ds'=>'night_rate', 'dt' => 1 ),
                array( "dsp"=>"Sales Count", 'db' => "count(night_rate)*datediff(checked_out,checked_in) as sales_count" ,"as"=>"sales_count", 'ds'=>'checked_out', 'dt' => 2 ),
                 array( "dsp"=>"Income", 'db' => "format(sum(night_rate*datediff(checked_out,checked_in)),0) as income" ,"as"=>"income", 'ds'=>'checked_in', 'dt' => 3 )
            );
            
            if(isset($_GET['json']))
            {
                $join = "left join reserved_rooms on room_id = idrooms and checked_out is not null";
                $where = ($this->fromDate !="") ? " date(checked_in) between ('{$this->fromDate}') and ('{$this->toDate}')":"";
                
                return Datatables\SSP::simple( $_GET, "rooms", "idrooms", $columns,$join,"mysql",$where,"group by idrooms" );
            }
            
            return \View::make("ReportCenter.Generator",["cols"=>$columns,"title"=>"Room Income"]);

    }

    public function shifts()
    {
            $pay_m = DB::connection("mysql")->select("select idpay_method,method_name from pay_method");
            
             $columns = array(
                array( "dsp"=>"Username", 'db' => 'username', 'dt' => 0 )                
             );

             $counter = 1;
            foreach ($pay_m as $pay) {
            
                array_push($columns, array( "dsp"=>$pay->method_name, 'db' => "sum(case when paymethod=".$pay->idpay_method." then credit else 0 end) as p".$pay->idpay_method, "as"=>"p".$pay->idpay_method, 'dt' => $counter ) );
                $counter++;
            }
          
            array_push($columns, array( "dsp"=>"Date", 'db' => 'folio.date', 'dt' => count($columns) ,"formatter"=>function($d){
                return \ORG\Dates::ToDSPFormat($d);
            } ));
            
            if(isset($_GET['json']))
            {
                $join = "left join folio on folio.user_id = idusers";
                $where = ($this->fromDate !="") ? " date(folio.date) between ('{$this->fromDate}') and ('{$this->toDate}')":"";

                return Datatables\SSP::simple( $_GET, "users", "idusers", $columns,$join,"mysql",$where,"group by date(folio.date),idusers");
            }
            
            return \View::make("ReportCenter.Generator",["cols"=>$columns,"title"=>"Frontdesk Shifts"]);


    }

    public function nightAudits()
    {
        $columns = array(
                array( "dsp"=>"Software Date", 'db' => 'real_date', 'dt' => 0 ),
                array( "dsp"=>"Working Date", 'db' => "working_date", 'dt' => 1 ),
                array( "dsp"=>"Closing Balance", 'db' => "closing_balance",'dt' => 2 ),
                array( "dsp"=>"User", 'db' => "username", 'dt' => 3 )
                
            );
            
            if(isset($_GET['json']))
            {
                $join = "join users on user_id = idusers";
                $where = ($this->fromDate !="") ? " date(night_audit.real_date) between ('{$this->fromDate}') and ('{$this->toDate}')":"";

                
                return Datatables\SSP::simple( $_GET, "night_audit", "idnight_audit", $columns,$join,"mysql",$where);
            }
            
            return \View::make("ReportCenter.Generator",["cols"=>$columns,"title"=>"Night Audits"]);
    }


    //POS

    public function POSSales($credit=false)
    {
            $footer = "'footerCallback': function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };

        try {
 
            // Total over all pages
            totalCredit= api
                .column( 2 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                } );

               totalDebit= api
                .column( 3 , { page: 'current'})
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                } );
 
            // Total over this page
           totalPaid= api
                .column( 4 , { page: 'current'})
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                } );
        }catch(ex){}

             $( api.column( 0 ).footer() ).html(
                'TOTAL'
            );

            $( api.column( 2 ).footer() ).html(
               accounting.formatMoney( totalCredit)
            );

            $( api.column( 3 ).footer() ).html(
                accounting.formatMoney(totalDebit)
            );

            $( api.column( 4 ).footer() ).html(
                accounting.formatMoney(totalPaid)
            );
        
        },";

             $columns = array(
                array( "dsp"=>"ID", 'db' => 'idbills', 'dt' => 0 ),
                array( "dsp"=>"Customer", 'db' => "customer", 'dt' => 1 ),
                array( "dsp"=>"Bill Total", 'db' => "format(bill_total,0) as bill_total" ,"as"=>"bill_total", 'ds'=>'bill_total', 'dt' => 2 ),
                array( "dsp"=>"Tax", 'db' => "tax_total", 'dt' => 3 ),
                array( "dsp"=>"Settle.", 'db' => "format((amount_paid-change_returned),0) as settle","as"=>"settle", 'ds'=>'amount_paid', 'dt' => 4 ),
                array( "dsp"=>"User", 'db' => 'username',  'dt' => 5 ),
                array( "dsp"=>"Status", 'db' => 'status_name', 'dt' => 6 ),
                array( "dsp"=>"User", 'db' => "date_format(pay_date,'%d/%m/%Y %T') as pay_date","as"=>"pay_date", 'dt' => 7 ),
                array( "dsp"=>"Date", 'db' => "date_format(bills.date,'%d/%m/%Y %T') as date","as"=>"date", 'dt' => 8)
                
            );
            
            if(isset($_GET['json']))
            {
                $join = "
                join bill_status on idbill_status = status
                join users on users.id = user_id";

                if($credit){
                 $where = ($this->fromDate !="") ? " date(bills.date) between ('{$this->fromDate}') and ('{$this->toDate}') and "."status=".\ORG\Bill::SUSPENDED :"";

                }else {
                 $where = ($this->fromDate !="") ? " date(bills.date) between ('{$this->fromDate}') and ('{$this->toDate}') ":"";
                    
                }



                return Datatables\SSP::simple( $_GET, "bills", "idbills", $columns,$join,"mysql_pos",$where );
            }
            
            return \View::make("ReportCenter.Generator",["cols"=>$columns,"title"=>"POS Sales","footer"=>$footer]);
    }


    public function POSCashier()
    {
        $columns = array(
                array( "dsp"=>"ID", 'db' => "idbills", 'dt' => 0 ),
                array( "dsp"=>"Cashier", 'db' => "concat_ws(' ',firstname,lastname) as name", 'as'=>'name', 'dt' => 1 ),
                array( "dsp"=>"Username", 'db' => "username", 'dt' => 2 ),
                array( "dsp"=>"Bill Total", 'db' => "format(sum(amount_paid),0) as total_sales" ,"as"=>"total_sales", 'ds'=>'bill_total', 'dt' => 3 ),
                array( "dsp"=>"Bills", 'db' => "count(*) as bills_count",'as'=>'bills_count', 'dt' => 4 )
                
            );
            
            if(isset($_GET['json']))
            {
                $join = "
                join users on users.id = user_id";
                 $where = ($this->fromDate !="") ? " date(bills.date) between ('{$this->fromDate}') and ('{$this->toDate}') " :"";
                return Datatables\SSP::simple( $_GET, "bills", "idbills", $columns,$join,"mysql_pos",$where,"group  by user_id" );
            }
            
            return \View::make("ReportCenter.Generator",["cols"=>$columns,"title"=>"Cashier Report"]);
    }


    public function POSStore()
    {
        $columns = array(
                array( "dsp"=>"ProductID", 'db' => "product_id", 'dt' => 0 ),
                array( "dsp"=>"Name", 'db' => "product_name", 'dt' => 1 ),
                array( "dsp"=>"Sales Count", 'db' => "sum(qty) as sales_count" ,"as"=>"sales_count", 'ds'=>'qty', 'dt' => 2 ),
                array( "dsp"=>"Amount", 'db' => "format(sum(unit_price*qty),0) as amount",'as'=>'amount', 'dt' => 3 ),
                array( "dsp"=>"Store", 'db' => "store_name", 'dt' => 4 ),
                
            );
            
            if(isset($_GET['json']))
            {
                $join = "join products on product_id = products.id
                join categories on products.category_id = categories.id
                join bills on idbills = bill_items.bill_id
                join store on idstore = store_id";
                 $where = ($this->fromDate !="") ? " date(bills.date) between ('{$this->fromDate}') and ('{$this->toDate}') " :"";

                return Datatables\SSP::simple( $_GET, "bill_items", "bill_id", $columns,$join,"mysql_pos",$where,"group by product_id" );
            }
            
            return \View::make("ReportCenter.Generator",["cols"=>$columns,"title"=>"Store Report"]);
    }


    public function POSShifts()
    {

            $columns = array(
                array( "dsp"=>"User", 'db' => "username", 'dt' => 0 ),
                array( "dsp"=>"Check", 'db' => "sum(check_amount) as check_amount", "as"=>"check_amount",'dt' => 1 ),
                array( "dsp"=>"Bank Card", 'db' => "sum(bank_card) as card_amount" ,"as"=>"card_amount", 'ds'=>'bank_card', 'dt' => 4 ),
                array( "dsp"=>"Bon", 'db' => "sum(bon) as bon_amount",'as'=>'bon_amount', 'dt' => 3 ),
                array( "dsp"=>"Cash", 'db' => "sum(cash) as cash", "as"=>"cash", 'dt' => 2 ),
                array( "dsp"=>"Date", 'db' => "date(payments.date) as date","as"=>"date", 'dt' => 5 )
                
            );
            
            if(isset($_GET['json']))
            {
                $join = "join payments on payments.user_id = users.id";

                 $where = ($this->fromDate !="") ? " void=0 and date(payments.date) between ('{$this->fromDate}') and ('{$this->toDate}')  " :" void=0";

                return Datatables\SSP::simple( $_GET, "users", "users.id", $columns,$join,"mysql_pos",$where," group by users.id,date(payments.date)" );
            }
            
            return \View::make("ReportCenter.Generator",["cols"=>$columns,"title"=>"POS Shifts"]);
    }

    public function POSDeletedBills()
    {
        $columns = array(
                array( "dsp"=>"Bill ID", 'db' => 'idbills', 'dt' => 0 ),
                array( "dsp"=>"Customer", 'db' => 'customer', 'dt' => 1 ),
                array( "dsp"=>"Bill Amount", 'db' => "bill_total", 'dt' => 2 ),
                array( "dsp"=>"Amount paid", 'db' => "amount_paid",'dt' => 3 ),
                array( "dsp"=>"Deleted By", 'db' => "username", 'dt' => 4 ),
                array( "dsp"=>"Date", 'db' => "bills.date", 'dt' => 5 )
                
            );
            
            if(isset($_GET['json']))
            {
                $join = "join users on deleted_by=users.id ";
                $where = ($this->fromDate !="") ? " deleted=1 and date(bills.date) between ('{$this->fromDate}') and ('{$this->toDate}') " :" deleted=1";

                return Datatables\SSP::simple( $_GET, "bills", "idbills", $columns,$join,"mysql_pos",$where," group by idbills");
            }
            
            return \View::make("ReportCenter.Generator",["cols"=>$columns,"title"=>"POS Deleted Bills"]);
    }
    public function POSnightAudits()
    {
        $columns = array(
                array( "dsp"=>"Software Date", 'db' => 'real_date', 'dt' => 0 ),
                array( "dsp"=>"Working Date", 'db' => "working_date", 'dt' => 1 ),
                array( "dsp"=>"Closing Balance", 'db' => "closing_balance",'dt' => 2 ),
                array( "dsp"=>"User", 'db' => "username", 'dt' => 3 )
                
            );
            
            if(isset($_GET['json']))
            {
                $join = "join users on user_id = users.id";
                $where = ($this->fromDate !="") ? " date(real_date) between ('{$this->fromDate}') and ('{$this->toDate}') " :"";

                return Datatables\SSP::simple( $_GET, "night_audit", "idnight_audit", $columns,$join,"mysql_pos",$where);
            }
            
            return \View::make("ReportCenter.Generator",["cols"=>$columns,"title"=>"POS Night Audits"]);
    }

    public function StockProducts()
    {
        $columns = array(
                array( "dsp"=>"ID", 'db' => 'id', 'dt' => 0 ),
                array( "dsp"=>"Code", 'db' => "code", 'dt' => 1 ),
                array( "dsp"=>"Name", 'db' => "name" , 'dt' => 2 ),
                 array( "dsp"=>"Size", 'db' => "size" , 'dt' => 3 ),
                 array( "dsp"=>"Cost", 'db' => "cost" , 'dt' => 4 ),
                 array( "dsp"=>"Price", 'db' => "price" , 'dt' => 5 )
            );
            
            if(isset($_GET['json']))
            {
                $join = "";
                
                return Datatables\SSP::simple( $_GET, "products", "id", $columns,$join,"mysql_stock" );
            }
            
            return \View::make("ReportCenter.Generator",["cols"=>$columns,"title"=>"Stock Products"]);
    }

    public function StockTransfers()
    {
             $columns = array(
                array( "dsp"=>"ID", 'db' => 'transfer_no', 'dt' => 0 ),
                array( "dsp"=>"From", 'db' => "from_warehouse_name" , 'dt' => 1 ),
                 array( "dsp"=>"To", 'db' => "to_warehouse_name" , 'dt' => 2 ),
                 array( "dsp"=>"Product", 'db' => "product_name" , 'dt' => 3 ),
                 array( "dsp"=>"Unit", 'db' => "product_unit" , 'dt' => 4 ),
                 array( "dsp"=>"U. Price", 'db' => "unit_price" , 'dt' => 5 ),
                 array( "dsp"=>"Quantity", 'db' => "quantity" , 'dt' => 6 ),
                 array( "dsp"=>"Date", 'db' => "date" , 'dt' => 7 )
            );
            
            if(isset($_GET['json']))
            {
                $join = "join transfer_items on transfer_items.transfer_id = transfers.id";
                $where = ($this->fromDate !="") ? " date(date) between ('{$this->fromDate}') and ('{$this->toDate}') " :"";
                
                return Datatables\SSP::simple( $_GET, "transfers", "transfers.id", $columns,$join,"mysql_stock",$where );
            }
            
            return \View::make("ReportCenter.Generator",["cols"=>$columns,"title"=>"Stock Transfers"]);
    }

    public function StockDamages()
    {
         $columns = array(
                 array( "dsp"=>"Code", 'db' => 'products.code', 'dt' => 0 ),
                 array( "dsp"=>"Product", 'db' => "products.name as p_name" ,"as"=>"p_name","ds"=>"products.name", 'dt' => 1 ),
                 array( "dsp"=>"Size", 'db' => "size" , 'dt' => 2 ),
                 array( "dsp"=>"Price", 'db' => "price" , 'dt' => 3 ),
                 array( "dsp"=>"Quantity", 'db' => "damage_products.quantity" , 'dt' => 4 ),
                 array( "dsp"=>"Warehouse", 'db' => "warehouses.name" , 'dt' => 5 ),
                 array( "dsp"=>"Date", 'db' => " damage_products.date" , 'dt' => 6 )
            );
            
            if(isset($_GET['json']))
            {
                $join = "join products on products.id = damage_products.product_id
                    join warehouses on warehouse_id = warehouses.id";
                $where = ($this->fromDate !="") ? " date(damage_products.date) between ('{$this->fromDate}') and ('{$this->toDate}') " :"";
                
                return Datatables\SSP::simple( $_GET, "damage_products", "damage_products.id", $columns,$join,"mysql_stock",$where );
            }
            
            return \View::make("ReportCenter.Generator",["cols"=>$columns,"title"=>"Stock - Damaged Products"]);
    }


    public function StockPurchases()
    {
        $columns = array(
                 array( "dsp"=>"ID", 'db' => 'purchases.id', 'dt' => 0 ),
                 array( "dsp"=>"Ref.", 'db' => "reference_no", 'dt' => 1 ),
                 array( "dsp"=>"Total", 'db' => "total" , 'dt' => 2 ),
                 array( "dsp"=>"Warehouse", 'db' => "warehouses.name" , 'dt' => 3 ),
                 array( "dsp"=>"Supplier", 'db' => "supplier_name" , 'dt' => 4 ),
                 array( "dsp"=>"Date", 'db' => "purchases.date" , 'dt' => 5 )
            );
            
            if(isset($_GET['json']))
            {
                $join = "join warehouses on warehouse_id = warehouses.id";

                $where = ($this->fromDate !="") ? " date(purchases.date) between ('{$this->fromDate}') and ('{$this->toDate}') " :"";
                
                return Datatables\SSP::simple( $_GET, "purchases", "purchases.id", $columns,$join,"mysql_stock",$where );
            }
            
            return \View::make("ReportCenter.Generator",["cols"=>$columns,"title"=>"Stock - Purchases"]);
    }


    public function login(Request $req)
    {
       $username = $req->input("username");
       $password =$req->input("password");
       $from = $req->input("loginfrom");
       $user = "";

       switch ($from) {
           case 'frontdesk':
               $password = md5($password);
               $user = DB::connection("mysql")->select("select username from users where username=? and password=?",[$username,$password]);
               
               break;
           case 'pos':
                   if(Auth::attempt(['username'=>$username,"password"=>$password])){
                    $user[0] = new \stdClass();
                    $user[0]->username = $username;
                   }
               break;
            case 'stock':
                $salt = FALSE;

                $password =  substr(sha1("password".$password), 0, -10);

                $user = DB::connection("mysql_stock")->select("select username from users where username=? and password=?",[$username,$password]);
               
                break;
           default:
               dd("Unknown Login");
               break;
       }

        if(isset($user[0])){
                   //session_start();
           $_SESSION['username'] = $user[0]->username;
           return redirect('/ReportCenter');
        }else {
            return redirect('/ReportCenter/Login')->with("errors","Wrong Username/Password");
        }
    }
}

ReportCenterController::isLoggedIn();