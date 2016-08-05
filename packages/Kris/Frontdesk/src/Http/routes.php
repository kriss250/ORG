<?php

/**
 * routes short summary.
 *
 * routes description.
 *
 * @version 1.0
 * @author kris
 */

namespace Kris\Frontdesk;
\Route::get("/fo/login",["as"=>"fo.login","uses"=>"Kris\Frontdesk\Controllers\UsersController@index"]);
\Route::get("/fo/logout",["as"=>"fo.logout","uses"=>"Kris\Frontdesk\Controllers\UsersController@logout"]);
\Route::post("/fo/login.attempt",["as"=>"fo.login.attempt","uses"=>"Kris\Frontdesk\Controllers\UsersController@login"]);

\Route::group(["middleware"=>"auth.fo"], function(){
    \Route::get("frontdesk/{view}",["uses"=>"Kris\Frontdesk\Controllers\OperationsController@home"]);
    \Route::get("fo/ajax/bookingdata",["uses"=>"Kris\Frontdesk\Controllers\OperationsController@getBookingData"]);
    \Route::get("fo/ajax/form/{form}",["uses"=>"Kris\Frontdesk\Controllers\OperationsController@forms"]);

    \Route::get("fo/print/{doc}",["uses"=>"Kris\Frontdesk\Controllers\OperationsController@_print"]);


    \Route::post("fo/reservation/refund/{id}",["uses"=>"Kris\Frontdesk\Controllers\ReservationsController@addRefund"]);
    \Route::get("fo/ajax/list/{list}",["uses"=>"Kris\Frontdesk\Controllers\OperationsController@lists"]);
    \Route::get("fo/guest/edit/{id}",["uses"=>"Kris\Frontdesk\Controllers\GuestController@edit"]);
    \Route::get("fo/section/frame/{name}",["uses"=>"Kris\Frontdesk\Controllers\OperationsController@frame"]);
    \Route::post("fo/section/frame/{name}",["uses"=>"Kris\Frontdesk\Controllers\OperationsController@frame"]);

    \Route::get("fo/roomView/{id}",["uses"=>"Kris\Frontdesk\Controllers\OperationsController@roomView"]);

    \Route::get("fo/ifrm/list",["uses"=>"Kris\Frontdesk\Controllers\ReservationsController@reservationList"]);
    \Route::post("fo/ifrm/list",["uses"=>"Kris\Frontdesk\Controllers\ReservationsController@listReservations"]);

    \Route::get("fo/ajax/searchCompany",["uses"=>"\Kris\Frontdesk\Controllers\OperationsController@findCompany"]);
    \Route::get("fo/ajax/setRoomStatus",["uses"=>"\Kris\Frontdesk\Controllers\OperationsController@setRoomStatus"]);

    /**
     * Reservations routes
     * */

    \Route::post("fo/ajax/saveWalkin",["uses"=>"\Kris\Frontdesk\Controllers\ReservationsController@walkin"]);
    \Route::post("fo/ajax/reserve",["uses"=>"\Kris\Frontdesk\Controllers\ReservationsController@reserve"]);

    \Route::get("fo/reservation/cancel/{id}",["uses"=>"\Kris\Frontdesk\Controllers\ReservationsController@cancel"]);
    \Route::get("fo/reservation/noshow/{id}",["uses"=>"\Kris\Frontdesk\Controllers\ReservationsController@noshow"]);

    \Route::get("fo/ajax/availableRooms",["uses"=>"\Kris\Frontdesk\Controllers\ReservationsController@getAvailableRooms"]);


    /**
     * Room Operations
     * */
    \Route::get("fo/checkin/{id}/{room}",["uses"=>"\Kris\Frontdesk\Controllers\ReservationsController@checkin"]);
    \Route::get("fo/checkout/{id}/{room}",["uses"=>"\Kris\Frontdesk\Controllers\ReservationsController@checkout"]);

    \Route::post("fo/reservation/update/{id}",["uses"=>"\Kris\Frontdesk\Controllers\ReservationsController@update"]);
    \Route::post("fo/reservation/addPayment/{id}",["uses"=>"\Kris\Frontdesk\Controllers\ReservationsController@addPayment"]);

    \Route::post("fo/reservation/shift/{id}",["uses"=>"\Kris\Frontdesk\Controllers\ReservationsController@shiftRoom"]);

    \Route::post("fo/reservation/addCharge/{id}",["uses"=>"\Kris\Frontdesk\Controllers\ReservationsController@addcharge"]);
    \Route::post("fo/addBanquetEvent",["uses"=>"Kris\Frontdesk\Controllers\OperationsController@addBanquetEvent"]);
    \Route::get("fo/deleteBanquetEvent",["uses"=>"Kris\Frontdesk\Controllers\OperationsController@deleteBanquetOrder"]);

    /**
     * Reports
     **/

    \Route::get("fo/reports/{room}",["uses"=>"\Kris\Frontdesk\Controllers\ReportsController@index"]);

    /**
     * Statements
     * */
    \Route::get("fo/statements/guest/{id}",["uses"=>"\Kris\Frontdesk\Controllers\StatementsController@guest"]);
    \Route::get("fo/statements/company/{id}",["uses"=>"\Kris\Frontdesk\Controllers\StatementsController@company"]);

});


