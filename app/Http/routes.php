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




Route::group(['middleware' => 'auth'],function(){
	// POS Routes
	Route::get("/POS",["as"=>"pos",function(){
		if(\Session::get("pos.mode")=="default")
        {
            return View::make("Pos/Home");
        }else {
            return View::make("Pos/Homev2");
        }
	}]);

  Route::resource("/Backoffice/PO","OrderController");
  Route::resource("/Backoffice/Invoice","InvoiceController");
  Route::get("/Backoffice/Invoice/delete/{x}",["uses"=>"InvoiceController@delete"]);
  Route::get("/Backoffice/Invoice/payment/delete/{x}",["uses"=>"InvoicePaymentController@delete"]);
  Route::get("/Backoffice/Invoice/showPayments/{x}",["uses"=>"InvoiceController@showPayments"]);
	Route::get("POS/NewDay",['as'=>'newday','uses'=>'SettingsController@newDay']);

	Route::get("/POS/Products/search/","ProductsController@searchProduct");
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

	Route::resource("POS/Settings","SettingsController");

	Route::resource("POS/Store","StoreController");
	Route::resource("POS/Users","UsersController");
    Route::resource("POS/Customers","CustomersController");
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
	Route::get("POS/Products/json","ProductsController@jsonReq");
	Route::get("POS/Bills/suspendedBills","BillsController@getSuspendedBills");
	Route::get("POS/Bills/billItems","BillsController@getBillItems");
	Route::get("POS/Bills/currentSales","BillsController@getCurrentSales");
	Route::resource("POS/Bills","BillsController");
	Route::post("POS/Products/CreateCustomProduct",['uses'=>'ProductsController@CreateCustomProduct']);
	Route::resource("POS/Products","ProductsController");

});


Route::group(['middleware' => 'auth'],function(){

    Route::resource("/Backoffice/users","UniversalUsersController");
    Route::get("/Backoffice",["as"=>"backoffice","uses"=>"BackofficeController@index"]);
    Route::get("/Backoffice/OccupiedRooms",["as"=>"backofficeOccupiedRooms","uses"=>"BackofficeController@OccupiedRooms"]);
    Route::resource("/Backoffice/cashbook","CashbookController");
    Route::resource("Backoffice/credits","CreditsController");
    Route::post("Backoffice/payCredit",["uses"=>"CreditsController@addPayment"]);
    Route::resource("/Backoffice/announcement","AnnouncementController");
    Route::resource("/Backoffice/payments","PaymentsController");
    Route::resource("/Backoffice/cashbook/transaction","CashbookTransactionController");
    Route::get("/Backoffice/Reports/POS/{name}","BackofficeReportController@index");
    Route::resource("/Backoffice/InvoicePayment","InvoicePaymentController");
    Route::get("/Backoffice/Credit/listCreditors",["uses"=>"CreditsController@listCreditors"]);
    Route::get("/Backoffice/Credit/paymentForm",["uses"=>"CreditsController@newPayment"]);
    Route::get("/Backoffice/InputSuggestions",["uses"=>"SuggestionsController@index"]);
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
    Route::get("/Backoffice/Debts/export",["uses"=>"POSCreditController@export"]);
    Route::get("/Backoffice/Settings/changePassword",function(){
    	return \View::make("Backoffice.ChangePassword");
    });
});
