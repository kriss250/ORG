<?php
namespace Kris\Frontdesk\Controllers;
use App\Http\Controllers\Controller;
use \Kris\Frontdesk;

/**
 * OperationsController short summary.
 *
 * OperationsController description.
 *
 * @version 1.0
 * @author kris
 */

class GuestController extends Controller
{
    public function index()
    {
        //return \View::make("Frontdesk::login");
    }

    public function edit($id)
    {
        $guest  = \Kris\Frontdesk\Guest::find($id);
        return \View::make("Frontdesk::addGuest",["guest"=>$guest]);
    }

    public function update()
    {
        $guest =\Kris\Frontdesk\Guest::find(\Request::input("guestid"));

        $guest->firstname = \Request::input("firstname");
        $guest->lastname = \Request::input("lastname");
        $guest->email = \Request::input("email");
        $guest->phone = \Request::input("phone");
        $guest->id_doc = \Request::input("id_doc");
        $guest->country = \Request::input("country");
        $guest->city = \Request::input("city");

        $guest->save();
    }


}