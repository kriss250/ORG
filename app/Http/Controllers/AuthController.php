<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return \View::make("Pos.Login");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $req)
    {
        $data = $req->all();

        $att = Auth::attempt([
            "username"=> $data['username'],
            "password"=>$data['password']
        ]);

        if($att)
        {
            $url = \Session::get('url.intended','/');
            \ORG\POS::Log("User ".(\Auth::user()->username)." loggedin | Destination : $url","default");
            return redirect($url);
        }else {
            return redirect()->route("login")->withErrors(['Wrong Username/Password']);
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
    public function destroy()
    {
        Auth::logout();

        return redirect()->route('login');
    }
}
