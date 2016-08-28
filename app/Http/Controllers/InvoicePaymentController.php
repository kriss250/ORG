<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use \ORG;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InvoicePaymentController extends Controller {

  /**
   * List Invoice's payments
   * @return Laravel Response
   */
  public function index()
  {

  }

  public function store()
  {

  }

  public function create()
  {
    return \View::make("Backoffice.InvoicePaymentForm");
  }
}
