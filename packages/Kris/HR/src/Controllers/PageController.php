<?php

/**
 * OperationsController short summary.
 *
 * OperationsController description.
 *
 * @version 1.0
 * @author kris
 */
namespace Kris\HR\Controllers;
use App\Http\Controllers\Controller;

class PageController extends Controller
{
    public function home()
    {
        return \View::make("HR::Home");
    }

    public function open($page)
    {
        return \View::make("HR::{$page}");
    }
}
