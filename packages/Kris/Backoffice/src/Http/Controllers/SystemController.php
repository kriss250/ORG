<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SystemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return \View::make("Backoffice/Admin/AdminDash");
    }

    public function jsProxy(\Request $req)
    {
        $curl = curl_init($_POST['url']);
        curl_exec($curl);
        curl_close($curl);
    }

}
