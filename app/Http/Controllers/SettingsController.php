<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use ORG;
use Auth;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return \View::make("Pos.Settings");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $req)
    {
        $data = $req->all();
        try {
            $tmp = \File::get('../app/Classes/POSSettings.tmpl');

            $con = strtr($tmp,[
                "{-RESTONAME-}"=>$data['resto_name'],
                "{-LOGO-}"=>$data['logo'],
                "{-CURRENCY-}"=>$data['currency'],
                "{-TAXRATE-}"=>$data['tax'],
                "{-PHONES-}"=>$data['phones'],
                "{-EMAIL-}"=>$data['email'],
                "{-WEBSITE-}"=>$data['website']
            ]);

            \File::put('../app/Classes/POSSettings.php',"<?php \n".$con);
            return redirect()->Back()->with("success","Settings Saved");
        }catch(Exception $ex){
            return redirect()->Back()->with("errors","Unable to save settings");
        }
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


    public function closeCashbooks()
    {
        $x = \DB::connection("mysql_backoffice")->update("update cash_book set closing_balance=balance where cashbookid>0");
        return $x > 0;
    }

    public function newDay()
    {
        $tsp1 =strtotime(date('y-m-d'));
        $tsp2 = strtotime(\ORG\Dates::$RESTODATE);


        if($tsp1>$tsp2)
        {


        $balance = DB::select("SELECT sum(amount_paid-change_returned) as balance,sum(bill_total) as billTotal FROM bills where status='".\ORG\Bill::SUSPENDED."' and deleted=0 and date(date)='".\ORG\Dates::$RESTODATE."'");
        $ctime = strtotime(\ORG\Dates::$RESTODATE);

        if($balance[0]->billTotal == $balance[0]->balance){
            try {

                $insert = DB::insert("insert into night_audit (real_date,working_date,new_date,closing_balance,user_id) values (?,?,?,?,?)",[
                    date('Y-m-d H:i:s'),\ORG\Dates::$RESTODT,date('Y-m-d H:i:s',$ctime + ( 24 * 60 * 60)),$balance[0]->balance,Auth::user()->id
                ]);

                //Update Store Quantity
                ProductsController::removeProductsFromStock();

                if($insert > 0 )
                {
                    //$this->closeCashbooks();
                    return redirect()->route("pos");
                }else {
                    return "Error";
                }
            }catch(Exception $ex){
                return "Error Setting new Day";
            }
        }else {
            return redirect()->route("pos")->withErrors(["There are suspended bills, Please make sure all suspended bills are paid before making a new day"]);
        }
        }else {
            return redirect()->route("pos")->withErrors(["Cannot make new day"]);
        }
    }

    public function newPassword(Request $req)
    {
        $c = new \stdClass();
        $c->errors = [];
        $c->message = "";

        if(strlen($req->input('password1'))<6){
            array_push($c->errors, 'Password must be at least 6 char. Long');
        }
        if($req->input('password1')!= $req->input('password2')){
            array_push($c->errors, 'Passwords do not matchs');
        }

        $old = \DB::select("select password from users where id=?",[Auth::user()->id])[0]->password;

        if(\Hash::check($req->input('oldpassword'), $old)){

            if(count($c->errors)==0){
                $up = DB::update("update users set password=? where id=?",[ \Hash::make($req->input('password1')),Auth::user()->id] );

                if($up>0){
                    $c->message = "Password Successfuly changed";
                }else {
                    array_push($c->errors, 'Error changing password , Please try again ');
                }
            }
        }else {
                    array_push($c->errors, 'Wrong password');

        }

        return json_encode($c);


    }
}
