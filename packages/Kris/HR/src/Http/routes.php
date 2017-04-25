<?php

/**
 * routes short summary.
 *
 * routes description.
 *
 * @version 1.0
 * @author kris
 */

namespace Kris\HR;
\Route::get("/HR/login",["as"=>"hr.login","uses"=>"Kris\Frontdesk\Controllers\UsersController@index"]);
\Route::get("/HR/logout",["as"=>"hr.logout","uses"=>"Kris\Frontdesk\Controllers\UsersController@logout"]);
\Route::post("/HR/login.attempt",["as"=>"hr.login.attempt","uses"=>"Kris\Frontdesk\Controllers\UsersController@login"]);

\Route::group(["middleware"=>"auth.fo","as"=>"hr"], function(){
    \Route::get("HR/",["uses"=>"Kris\HR\Controllers\PageController@home"]);
    \Route::get("HR/page/{page}",["uses"=>"Kris\HR\Controllers\PageController@open"]);
    \Route::resource("HR/department","\Kris\HR\Controllers\DepartmentController");
    \Route::resource("HR/post","\Kris\HR\Controllers\PostController");
    \Route::resource("HR/bank","\Kris\HR\Controllers\BankController");
    \Route::resource("HR/charge","\Kris\HR\Controllers\ChargeController");

    \Route::post("HR/employeeCharge/save","\Kris\HR\Controllers\ChargeController@saveEmployeeCharge");

    \Route::get("HR/employeeCharge/edit/{x}","\Kris\HR\Controllers\ChargeController@editEmpCharge");
    \Route::get("HR/employeeCharge/remove/{x}","\Kris\HR\Controllers\ChargeController@removeEmpCharge");

    \Route::resource("HR/tax","\Kris\HR\Controllers\TaxController");
    \Route::resource("HR/leave","\Kris\HR\Controllers\LeaveController");
    \Route::get("HR/leave/remove/{id}","\Kris\HR\Controllers\LeaveController@remove");
    \Route::resource("HR/employee","\Kris\HR\Controllers\EmployeeController");
    \Route::resource("HR/payroll","\Kris\HR\Controllers\PayrollController");
    \Route::get("HR/payroll/remove/{id}","\Kris\HR\Controllers\PayrollController@remove");

    \Route::resource("HR/advance","\Kris\HR\Controllers\AdvanceController");
    \Route::get("HR/advance/remove/{id}","\Kris\HR\Controllers\AdvanceController@remove");

    \Route::resource("HR/absence","\Kris\HR\Controllers\AbsenceController");

    \Route::get("HR/posts/get/{dpid?}",["uses"=>"Kris\HR\Controllers\PostController@getPosts"]);
});


