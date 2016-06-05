<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use \DB;
use \ORG;

class BackofficeController extends Controller
{

    public function __construct()
    {
        if(\Auth::user()->level < 7)
        {
            abort(403);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data = DB::connection("mysql_backoffice")->select("select buying,selling,currency,date(date) as date from exchange_rate order by id desc limit 2");
        $sales = ["pos_sales"=>0,"fo_sales"=>0,"pos_credit"=>0,"fo_credit"=>0,"total_paid"=>0];
        //if not updated
        if($data[0]->date !=date("Y-m-d"))
        {
            try {
                $d = $this->retreiveExchangeRates();

            }catch(\Exception $ex)
            {
            }
        }

        $date = \ORG\Dates::$RESTODATE;
        $d2 = $date;

        $d = new \DateTime($date);
        $d->sub(new \DateInterval('P6D'));

        $d3 = new \DateTime($d->format('Y-m-d'));
        $d3->sub(new \DateInterval('P7D'));

        $purchases = \DB::connection("mysql_stock")->select("SELECT coalesce(sum(inv_total),0) as amount,warehouses.name FROM warehouses left join purchases on warehouses.id = warehouse_id where date =? group by warehouse_id ",[$date]);
        $requisitions = \DB::connection("mysql_stock")->select("select department_name, coalesce((sub_total),0) as amount from requisition
            join requisition_items on requisition_items.requisition_id = requisition.idrequisition
            join departments on departments.iddepartment=department_id
        where date(date)=?
        group by department_id",[$date]);

        $week_1 = \DB::select("SELECT  date_format(date,'%W') as day,coalesce(sum(bill_total),0) as total FROM `bills` where deleted=0 and status not in( ".\ORG\Bill::OFFTARIFF.",".\ORG\Bill::SUSPENDED.") and date(bills.date) between '{$d->format('Y-m-d')}' and '$d2' group by date_format(date,'%W') order by date_format(date,'%w') asc");
        $week_2 = \DB::select("SELECT  date_format(date,'%W') as day,coalesce(sum(bill_total),0) as total FROM `bills` where deleted=0 and status not in( ".\ORG\Bill::OFFTARIFF.",".\ORG\Bill::SUSPENDED.") and date(bills.date) between   '{$d3->format('Y-m-d')}' and '{$d->format('Y-m-d')}'  group by date_format(date,'%W') order by date_format(date,'%w') asc");

        $week_sales = [$week_2,$week_1];


        $cashbooks = \DB::connection("mysql_backoffice")->select("select * from cash_book");
        $logs  = \DB::select("select username,action,logs.date from logs join users on users.id = user_id order by idlogs desc limit 6");

       // $fo_sales = \DB::connection("mysql_book")->select()
        $pos_sales = \DB::select("select status,coalesce(sum(bill_total),0) as amount from bills where status not in (".\ORG\Bill::SUSPENDED.",".\ORG\Bill::OFFTARIFF.") and date(date)=? group by status",[$date]);
        $pos_payments = \DB::select("SELECT coalesce(sum(bank_card+cash+check_amount),0) as amount FROM payments where date(date)=? and void=0",[$date]);
        $fo_payments = \DB::connection("mysql_book")->select("SELECT coalesce(sum(credit),0) as amount FROM folio where void=0 and date(date)=?",[$date]);
        $fo_sales = \DB::connection("mysql_book")->select("select coalesce(sum(night_rate*DATEDIFF(date(checkout),date(checkin))),0) as  amount,pay_by_credit from reservations
            join reserved_rooms on reserved_rooms.reservation_id = idreservation
        where status=5 and date(reservations.date)=? group by pay_by_credit",[$date]);


         foreach ($fo_sales as $fs)
         {
            if($fs->pay_by_credit>0){
                $sales['fo_credit']=$fs->amount;
            }

            $sales['fo_sales'] += $fs->amount;
         }

         foreach($pos_sales as $ps)
         {
             if($ps->status == \ORG\Bill::CREDIT)
             {
                 $sales['pos_credit'] = $ps->amount;
             }

             $sales['pos_sales'] +=  $ps->amount;
         }

         $sales['total_paid'] = isset($pos_payments[0]->amount) ? $pos_payments[0]->amount : 0;
         $sales['total_paid'] += isset($fo_payments[0]->amount) ? $fo_payments[0]->amount: 0 ;

         if(\Auth::user()->level>6 && \Auth::user()->level < 9)
         {
             return \View::make("/Backoffice/dashboard2",["sales"=>$sales,'exchangerates'=>$data,"cashbooks"=>$cashbooks,"logs"=>$logs,"weeksales"=>$week_sales,"purchases"=>$purchases,"requisitions"=>$requisitions]);
         }

         return \View::make("/Backoffice/index",["sales"=>$sales,'exchangerates'=>$data,"cashbooks"=>$cashbooks,"logs"=>$logs,"weeksales"=>$week_sales,"purchases"=>$purchases,"requisitions"=>$requisitions]);
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
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
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
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
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

    public function OccupiedRooms()
    {
        $sql = "select concat_ws(' ',firstname,lastname)as guest,room_number,concat(date_format(checkin,'%d/%M'),' - ',date_format(checkout,'%d/%M')) dates,night_rate,due_amount,balance_amount from reserved_rooms
            join rooms on rooms.idrooms = room_id
            join guest on guest.id_guest = guest_in
            join accounts on accounts.reservation_id = reserved_rooms.reservation_id
            where checked_in is not null and checked_out is null limit 12";

        $data =  \DB::connection("mysql_book")->select($sql);
        return json_encode($data);
    }

    public function retreiveExchangeRates(){
        $data = "";
        $ctx = stream_context_create(array('http'=>
            array(
                'timeout' => 20,
            )
        ));
        $data = ""; //file_get_contents("http://bnr.rw/index.php?id=204&no_cache=1",false,$ctx);


        $start = strpos($data, "<table>");
        $stop = strpos($data, '<div id="rightbar" class="sidebar">');
        $len = ($stop-$start)-12;
        $table = substr($data,$start,$len);
        $eur = strpos($table ,"<td>EUR");
        $eur_end = strpos($table ,"</tr>",$eur);

        $usd = strpos($table ,"<td>USD");
        $usd_end = strpos($table ,"</tr>",$usd);

        $u =str_replace(['<td>','</td>','<tr>','</tr>'],',',substr($table, $eur,($eur_end-$eur)));
        $u = trim($u,',');

        $EURO = explode(",,", $u);
        array_pop($EURO);


        $u =str_replace(['<td>','</td>','<tr>','</tr>'],',',substr($table, $usd,($usd_end-$usd)));
        $u = trim($u,',');

        $USD = explode(",,", $u);
        array_pop($USD);


        \DB::connection("mysql_backoffice")->insert("insert into exchange_rate (currency,selling,buying) values(?,?,?)",$USD);
        \DB::connection("mysql_backoffice")->insert("insert into exchange_rate (currency,selling,buying) values(?,?,?)",$EURO);

        $data =array(new \stdClass(),new \stdClass());
        $data[0]->currency = "USD";
        $data[0]->selling = $USD[1];
        $data[0]->buying = $USD[2];
        $data[0]->date = date("Y-m-d");

        $data[1]->currency = "EUR";
        $data[1]->selling = $EURO[1];
        $data[1]->buying = $EURO[2];
        $data[1]->date = date("Y-m-d");

        return $data;
    }

    public function search($query)
    {
        $keywords = ["#","order","bill","cashier","room"];
        $key ="";
        for($i=0;$i<count($keywords);$i++)
        {
            if(strstr($query,strtolower($keywords[$i])))
            {
                $key =strtolower( $keywords[$i]);
                break;
            }
        }

       if(is_numeric($query))
       {
           $data = \DB::select("select 'location=Bills' as location,concat('Bill : #',idbills,' Bill Total : ',bill_total) as text,idbills as ID from bills where idbills like ? limit 10",["%".$query."%"]);
           return json_encode( $data);
       }

       switch ($key)
       {
       	  case "order":
                 $id = "%".explode(' ',$query)[1]."%";
                 $data = \DB::select("select idbills,customer as text,bill_total from bills where idbills like ? limit 10",[$id]);
                 print_r($data);
                 break;
             case "bill":
                 $id = "%".explode(' ',$query)[1]."%";
                 $data = \DB::select("select concat_ws(' : ',idbills,customer,bill_total) as text,idbills from bills where idbills like ? limit 10",[$id]);
                 return json_encode( $data);

             case "cashier":
                 $cashierid = "%".explode(' ',$query)[1]."%";
                 $data = \DB::select("select idbills,bill_total from bills where user_id like ? order by date desc limit 20",[$cashierid]);
                 return json_encode( $data);
             case "room":
                 try{
                     $room = "%".explode(' ',$query)[1];
                 }catch(\Exception $x){$room="";}
                 $data = \DB::connection("mysql_book")->select("select concat(room_number,' : ',type_name) as text,'location=rooms' as location,idrooms as ID from rooms
                    join room_status on room_status.status_code = status
                    join room_types on room_types.idroom_types = type_id where room_number like ?",[$room]);
                 return json_encode( $data);
             default:
                 $data = \DB::select("select 'location=Bills' as location,concat('Bill : #',idbills,' customer : ',customer) as text,idbills as ID from bills where customer like ? order by idbills desc limit 10",["%".$query."%"]);
                 return json_encode( $data);

       }

    }
}
