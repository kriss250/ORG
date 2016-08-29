<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Credit;
use App\Creditor;
use App\CreditPayment;

class CreditsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
      $orders = \App\Credit::limit("50")->get();
      return \View::make("Backoffice.CreditOrdersList",["orders"=>$orders]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return \View::make('Backoffice.NewCredit');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $req)
    {
      $data = $req->all();
      $rules = ["amount"=>"required|numeric","voucher"=>"required","creditor"=>"required"];
      $validator = \Validator::make($data,$rules);

      if(!$validator->fails())
      {
        $creditor = \App\Creditor::firstOrCreate([
          "name"=>trim($data['creditor']),
          "paid_amount"=>0,
        ]);

        $creditor->due_amount = $creditor->due_amount+$data['amount'];
        $creditor->save();

        $credit  = \App\Credit::create([
          "creditor_id"=>$creditor->idcreditors,
          "voucher"=>$data['voucher'],
          "amount"=>$data['amount'],
          "description"=>$data["description"],
          "user_id"=>\Auth::user()->id,
          "date"=>$data['date']
        ]);

        if(is_null($credit))
        {
            return redirect()->back()->withErrors(["Error saving data"]);
        }else {
          return redirect()->back()->with(["msg"=>"Order saved successfully"]);
        }
      }else {
        return redirect()->back()->withErrors($validator->errors());
      }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function update($id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
    }

    public function delete($id)
    {
    }

    public function newPayment()
    {
      return \View::make("Backoffice.CreditPaymentForm");
    }

    public function addPayment()
    {
      $data = \Request::all();
      $validator = \Validator::make($data,["voucher"=>"required","amount"=>"required|numeric","date"=>"required"]);

      if($validator->fails()){
        return redirect()->back()->withErrors($validator->errors());
      }else {
        $credit = \App\Credit::where(["voucher"=>$data['voucher']])->first();
       $pay = \App\CreditPayment::create([
          "amount"=>$data['amount'],
          "credit_id"=>$credit->id,
          "description"=>$data['description']
        ]);


        $credit->paid_amount =$credit->paid_amount + $data['amount'];
        $creditor = $credit->creditor;
        $creditor->paid_amount = $creditor->paid_amount + $data['amount'];

        $creditor->save();
        $credit->save();

        if($pay!=null)
        {
          return redirect()->back()->with(["msg"=>"Payment Saved"]);
        }else {
          return redirect()->back()->withErrors(["Error saving payment"]);
        }
      }
    }
}
