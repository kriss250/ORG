<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //list customers
        $firstname = "";
        $lastname = "";
        $company ="";
        $fo_customers = null;
        $pos_customers =null;
        $fo_companies = null;

        if(isset($_GET['name']) || isset($_GET['company']))
        {
            $names= isset($_GET['name']) ? explode(' ',$_GET['name']) :[];
            $company = isset($_GET['company']) ?  $_GET['company'] : "";

            $sql = "select id_guest,firstname,lastname,email,phone,birthdate,country,id_doc,'type' from guest where (firstname like ? and lastname like ?) or (lastname like ? and firstname like ?)";
            $sqlx = "select idcompanies,name,email,phone from companies where name like ?";
            $sql2 = "SELECT distinct customer FROM bills where customer like ? or customer like ?";

            $firstname = isset($names[0]) ? "%".$names[0] : "";
            $lastname = isset($names[1]) ? "%".$names[1] : "";
            if(isset($_GET['name']) && strlen($_GET['name']) > 2){
                $fo_customers = \DB::connection("mysql_book")->select($sql,['%'.$firstname,'%'.$lastname,'%'.$firstname,'%'.$lastname]);
            }
            if(isset($_GET['company']) && strlen($_GET['company']) > 1){
                $fo_companies =  \DB::connection("mysql_book")->select($sqlx,['%'.$company]);
            }

            
            $namex = strlen($_GET['name']) > 2 ?  implode(" ",$names) : $company;
            $company = strlen($company) > 1 ?$company : $namex;
            $pos_customers = \DB::select($sql2,["%".$company."%","%$namex"]);
        }

        return \View::make("Backoffice.CustomerSearch",["fog"=>$fo_customers,"foc"=>$fo_companies,"pos"=>$pos_customers]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //show particular customer
    }

}
