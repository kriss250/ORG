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
        $d->sub(new \DateInterval('P7D'));

        $payments = \DB::select("select * from payments where void=0 and date(date)=? limit 5",[$date]);
        $week_sales = \DB::select("SELECT  date_format(date,'%W') as day,sum(bill_total) as total FROM `bills` where deleted=0 and status not in( ".\ORG\Bill::OFFTARIFF.",".\ORG\Bill::SUSPENDED.") and date(bills.date) between  ' ".$d->format('Y-m-d')."' and '$d2' group by date_format(date,'%W') order by date asc");
         $cashbooks = \DB::connection("mysql_backoffice")->select("select * from cash_book");
         $logs  = \DB::select("select username,action,logs.date from logs join users on users.id = user_id order by idlogs desc limit 6");
         $bills= \DB::select("select sum(bill_total) as total,status from bills where date(bills.date)='$date'  and status in (".\ORG\Bill::PAID.",".\ORG\Bill::ASSIGNED.",".\ORG\Bill::CREDIT.") and deleted=0 group by status order by status desc");

         if(\Auth::user()->level>6 && \Auth::user()->level < 9){
             return \View::make("/Backoffice/dashboard2",['exchangerates'=>$data,"cashbooks"=>$cashbooks,"logs"=>$logs,"bills"=>$bills,"weeksales"=>$week_sales,"payments"=>$payments]);

         }

        return \View::make("/Backoffice/index",['exchangerates'=>$data,"cashbooks"=>$cashbooks,"logs"=>$logs,"bills"=>$bills,"weeksales"=>$week_sales,"payments"=>$payments]);
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

    public function retreiveExchangeRates(){
        $data = "";
         $data = file_get_contents("http://bnr.rw/index.php?id=204&no_cache=1");


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
        $keywords = ["#","order","bill","cashier"];
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

             default:
                 $data = \DB::select("select 'location=Bills' as location,concat('Bill : #',idbills,' customer : ',customer) as text,idbills as ID from bills where customer like ? order by idbills desc limit 10",["%".$query."%"]);
                 return json_encode( $data);

       }


        //\DB::select("select idbills,customer,bill_total from bills where idbills=?");
        //\DB::select("select idbills,customer,bill_total from bills where room=?");
         //\DB
    }
}
