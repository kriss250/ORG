<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
 */
Route::get("/",function(){
    return view("index/index");
});

Route::post("/errors/report",function(){
    \App\Exceptions\Handler::emailLog(\Request::input("msg"));
});

Route::get("/Frontdesk/Reports",function(){
return view("ORGFrontdesk/Reports/index");
});


Route::get('/ORGFrontdesk/views/floors', [
    'uses' => 'FloorViewController@Display'
]);

Route::get('/ORGFrontdesk/views/BookingView', [
    'uses' => 'BookingViewController@index'
]);

Route::get('/ORGFrontdesk/views/BackofficeBookingView', [
    'uses' => 'BookingViewController@indexv2'
]);

Route::get('/ORGFrontdesk/views/BookingView/data', [
    'uses' => 'BookingViewController@getBookingData'
]);

Route::get("/ORGFrontdesk/views/calendar",[
    "uses" => "CalendarViewController@DisplayCal"
    ]);

Route::get("/ORGFrontdesk/Reports/Ajax/{rp}",[
    "uses"=>"ReportsController@Ajax"
    ]);

Route::get("/ORGFrontdesk/Reports/{report}",[
    "uses"=>"ReportsController@index"
]);


Route::get("/ReportCenter/",["uses"=>"ReportCenterController@index","as"=>"reportcenter"]);

Route::get("ReportCenter/api",["uses"=>"ReportCenterController@jsonApi","as"=>"generatorApi"]);

Route::get("ReportCenter/rp/{report}",[
    "uses"=>"ReportCenterController@generateReport",
    'as'=>'generatereport'
]);

Route::get("ReportCenter/Login",function(){
    return \View::make("ReportCenter.Login");
});



Route::post("ReportCenter/Login",['uses'=>'ReportCenterController@login']);


Route::get("/POS/login/",['uses'=>"AuthController@create","as"=>"login"]);
Route::post("/POS/login/","AuthController@store");
Route::get("/POS/logout/",['uses'=>"AuthController@destroy",'as'=>'logout']);



Route::get("POS/Products/json","ProductsController@jsonReq");
Route::get("/POS/Products/search/","ProductsController@searchProduct");
Route::post("/POS/orders/save/","OrdersController@saveOrder");
Route::get("POS/Orders/PrintOrder/{id}",["as"=>"printorder","uses"=>"OrdersController@printOrder"]);
Route::post("POS/Waiters/changePIN","WaiterController@changePIN");
Route::group(['middleware' => 'auth'],function(){
	// POS Routes
    Route::get("/POS",["as"=>"pos",function(){


        if(isset($_GET['sales_mode']))
        {
            if(\App\SalesMode::getMode() !=""){
                setcookie('sales_mode',$_GET['sales_mode'],time()+86400*90,"/");
                $_COOKIE['sales_mode'] = $_GET['sales_mode'];
            }else {
                setcookie('sales_mode',$_GET['sales_mode'],time()+86400*90,"/");
            }
        }else if(\App\SalesMode::getMode() =="") {
            setcookie('sales_mode',\App\SalesMode::NORMAL,time()+86400*90,"/");
            $_COOKIE['sales_mode'] =1;
        }

	    if(\Session::get("pos.mode")=="default")
        {
            return View::make("Pos/Home");
        }else {
            return View::make("Pos/Homev2");
        }
    }]);

    Route::get("/Setup","SettingsController@AppSetup");
    Route::post("/Setup/set","SettingsController@setup");
    Route::get("/Backoffice/dasboard2","BackofficeController@dashboard2");

    Route::resource("/Backoffice/PO","OrderController");
    Route::resource("/Backoffice/Invoice","InvoiceController");
    Route::resource("/Backoffice/Proforma","ProformaController");
    Route::get("/Backoffice/Invoice/delete/{x}",["uses"=>"InvoiceController@delete"]);

    Route::get("/Backoffice/Proforma/delete/{x}",["uses"=>"ProformaController@delete"]);

    Route::get("/Backoffice/Invoice/payment/delete/{x}",["uses"=>"InvoicePaymentController@delete"]);
    Route::get("/Backoffice/Invoice/showPayments/{x}",["uses"=>"InvoiceController@showPayments"]);
	Route::get("POS/NewDay",['as'=>'newday','uses'=>'SettingsController@newDay']);


	Route::post("/POS/Bills/assignBill/","BillsController@assignBill");
  Route::post("/POS/Bills/paySuspended/","BillsController@paySuspendedBill");
  Route::post("/POS/Bills/updateBill/","BillsController@updateBill");
  Route::post("/POS/Bills/suspend/","BillsController@suspend");

  Route::post("/POS/Bills/pay/","BillsController@pay");
	Route::get("/POS/Bills/shareBill/","BillsController@shareBill");
	Route::get("/POS/Bills/assignedBills/","BillsController@assignedList");
    Route::get("/POS/Bills/checkRoom/","BillsController@checkRoom");
	Route::post("/POS/Bills/assignBill/pay","BillsController@payAssignedBill");
	Route::post("/POS/Bills/creditBill/pay","BillsController@payCreditBill");

    Route::get("/POS/Bills/deleteBillPayments","BillsController@deleteBillPayments");
    Route::post("/POS/GeneralReport",["uses"=>"POSReportController@GenerateReport","as"=>"POSGeneralReport"]);

	Route::get("POS/Bills/PrintBill/{id}",["as"=>"printbill","uses"=>"BillsController@printBill"]);

	Route::get("/POS/Settings/changePassword",function(){
		return \View::make("Pos.ChangePassword");
	});

	Route::post("/POS/Settings/newPassword",['uses'=>"SettingsController@newPassword"]);
    Route::get("/POS/productPrice",["uses"=>"ProductsController@productPrice"]);
    Route::post("/POS/productPrice/update",["uses"=>"ProductsController@productPriceUpdate"]);

    Route::get("/POS/productCategory",["uses"=>'ProductsController@categoryStore']);
    Route::post("/POS/setProductCategory/",["uses"=>'ProductsController@setCategoryStore']);
    Route::get("/POS/removeCatStore/{cat}/{store}/",["uses"=>'ProductsController@removeCatStore']);
	Route::resource("POS/Settings","SettingsController");


	Route::resource("POS/Store","StoreController");
	Route::resource("POS/Users","UsersController");
    Route::resource("POS/Customers","CustomersController");
    Route::get("POS/Customers/Bill/Finder","CustomersController@billFinder");
    Route::get("POS/Customers/Bill/Finder/{id}","CustomersController@printBill");

    Route::resource("BusinessCustomers","CustomerController");
    Route::get("/Statement/{where}/{id}/{company}/{individual}/","StatementController@ShowStatement");
	Route::resource("POS/Waiters","WaiterController");

	Route::resource("POS/Products/Categories","ProductsCategoryController");
    Route::resource("FO/Reservations","ReservationController");

    Route::get("POS/Products/markAsFavorite/{Product}/{state}","ProductsController@markAsFavorite");

	Route::get("POS/Reports/{name}",['uses'=>"POSReportController@index","as"=>"POSReports"]);
	Route::post("POS/Reports/{name}",['uses'=>"POSReportController@getData","as"=>"POSReportsPOST"]);

  Route::get("POS/Products/jsonSubCats",['uses'=>'ProductsSubCategoryController@ajaxGetSubCategories']);
	Route::resource("POS/Products/SubCategories","ProductsSubCategoryController");

	Route::get("POS/Bills/suspendedBills","BillsController@getSuspendedBills");
	Route::get("POS/Bills/billItems","BillsController@getBillItems");
	Route::get("POS/Bills/currentSales","BillsController@getCurrentSales");
	Route::resource("POS/Bills","BillsController");
	Route::post("POS/Products/CreateCustomProduct",['uses'=>'ProductsController@CreateCustomProduct']);
	Route::resource("POS/Products","ProductsController");
    Route::resource("POS/Tables","TableController");
    Route::get("POS/Tables/delete/{id}","TableController@delete");
    Route::get("/POS/orders/getorders","OrdersController@getOrders");
    Route::get("/POS/orders/getorder","OrdersController@getOrder");

});

Route::group(['middleware' => 'auth'],function(){
    Route::get("/Backoffice/InputSuggestions",["uses"=>"SuggestionsController@index"]);
    Route::resource("/Backoffice/users","UniversalUsersController");
    Route::get("/Backoffice/users/toggleActivation/{user}/","UniversalUsersController@activationToggle");
    Route::get("/Backoffice",["as"=>"backoffice","uses"=>"BackofficeController@index"]);
    Route::get("/Backoffice/OccupiedRooms",["as"=>"backofficeOccupiedRooms","uses"=>"BackofficeController@OccupiedRooms"]);
    Route::resource("/Backoffice/cashbook","CashbookController");
    Route::resource("Backoffice/credits","CreditsController");

    Route::get("Backoffice/SysAdmin","SystemController@index");
    Route::post("Backoffice/SysAdmin/proxy/","SystemController@jsProxy");

    Route::get("Backoffice/credits/delete/{id}","CreditsController@deleteCredit");
    Route::get("Backoffice/credits/show/payments/{id}","CreditsController@showPayments");
    Route::get("Backoffice/credits/delete/payments/{id}","CreditsController@deletePayment");
    Route::post("Backoffice/payCredit",["uses"=>"CreditsController@addPayment"]);
    Route::resource("/Backoffice/announcement","AnnouncementController");
    Route::resource("/Backoffice/payments","PaymentsController");
    Route::resource("/Backoffice/cashbook/transaction","CashbookTransactionController");
    Route::get("/Backoffice/cashbook/transaction/print/{id}","CashbookTransactionController@printTrans");
    Route::get("/Backoffice/Reports/POS/{name}","BackofficeReportController@index");
    Route::resource("/Backoffice/InvoicePayment","InvoicePaymentController");
    Route::get("/Backoffice/Credit/listCreditors",["uses"=>"CreditsController@listCreditors"]);
    Route::get("/Backoffice/Credit/paymentForm",["uses"=>"CreditsController@newPayment"]);

    Route::get("/Backoffice/Search/{query}/",['uses'=>'BackofficeController@search','as'=>'BackofficeSearch']);
    Route::get("/Backoffice/itemPreview/",function(){
         return \View::make("Backoffice.ItemPreview");
     });
    Route::get("/Backoffice/Reports/Header",function(){
        return \View::make("Backoffice.Reports.ReportHeader");
    });

    Route::get("/Backoffice/Debts/unexported",["uses"=>"POSCreditController@unexportedDebts"]);
    Route::get("/Backoffice/Debts/internal",["uses"=>"POSCreditController@internalDebts"]);
    Route::get("/Backoffice/Debts/external",["uses"=>"POSCreditController@externalDebts"]);
    Route::get("/Backoffice/Debts/json",["uses"=>"InvoiceController@getDebts"]);
    Route::get("/Backoffice/Debts/browse",["uses"=>"InvoiceController@browseDebts"]);
    Route::get("/Backoffice/Debts/export",["uses"=>"POSCreditController@export"]);
    Route::get("/Backoffice/Settings/changePassword",function(){
    	return \View::make("Backoffice.ChangePassword");
    });
});


Route::group([],function(){
    Route::get("/Order",["as"=>"order",function(){
	    return View::make("Pos/OrderIndex");
    }]);

    Route::get("/Order/new",["as"=>"newOrder",function(){
	    return View::make("Pos/Order");
    }]);


});

Route::get("rt",function(){

   $API = new \RouterOS\RouterOSAPI();
$API->debug = false;
if ($API->connect('105.179.5.126', 'kriss', 'Kriss123')) {
    $API->write('/ip/hotspot/user/profile/print');
    //$API->write("=name=okma", false);
    //$API->write("=limit-uptime=156", false);
    //$API->write("=profile=nolimit",false);
    //$API->write("=password=adminxx", true);

    $READ = $API->read(false);
    $ARRAY = $API->parseResponse($READ);
    print_r($ARRAY);
    $API->disconnect();
}

});