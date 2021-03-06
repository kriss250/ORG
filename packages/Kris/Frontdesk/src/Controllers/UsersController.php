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

class UsersController extends Controller
{
    public function index()
    {
        return \View::make("Frontdesk::login");
    }

    public function login()
    {
        $username  = \Request::input("username");
        $password  = \Request::input("password");

        $user  = \Kris\Frontdesk\User::where("username","=",$username)
            ->where("password","=",md5($password))->where("is_active","1")->get()->first();

        if(!is_null($user)){
            \Session::put("fo_user",$user);
            \FO::log("Logged in from {$_SERVER['REMOTE_ADDR']}");
            $url = \Session::get('url.intended','/frontdesk/standard');
            return redirect()->to($url);
        }else
        {
            return redirect()->back()->withInput()->withErrors(["Wrong username / Password"]);
        }

    }

    public function logout()
    {
        \Session::remove("fo_user");
        return $this->index();
    }
}