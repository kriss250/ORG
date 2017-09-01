<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use App\InvoicePayment;
use App\Invoice;
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
      $data = \Request::all();
      $id = $data['invoice_id'];

      $invoice = null;
      $validator = \Validator::make($data,[
        "invoice"=>"required",
        "amount"=>"required|numeric",
        "mode"=>"required|numeric"
        ]);


      if(!is_numeric($data["invoice_id"]))
      {
          $validator->getMessageBag()->add('invoiceid', 'Invalid invoice number format , use "code/year"');
      }else {
          $invoice  = Invoice::find($data["invoice_id"]);
      }

      if($invoice == null)
      {
          $validator->getMessageBag()->add('invoiceid', 'Invalid invoice number');
      }

      if($validator->fails() || $invoice == null )
      {
          return redirect()->back()->withInput()->withErrors($validator->all());
      }else {
          $p = $invoice->payment()->create([
            "amount"=>$data['amount'],
            "description"=>$data['description'],
            "pay_mode"=>$data['mode'],
            "wh_vat"=>isset($data['vat']) ? $data['vat'] : 0,
            "wht"=>isset($data['wht']) ? $data['wht'] : 0
          ]);
          if($p!=null)
          {
              return redirect()->back()->with(["msg"=>"Payment saved"]);
          }
      }
  }

  public function create()
  {
    return \View::make("Backoffice.InvoicePaymentForm");
  }

  public function delete($id)
  {
    \App\InvoicePayment::find($id)->delete();
    return redirect()->back();
  }
}
