
<?php
$imported="";
$imported = (isset($_imported) && $_imported=="1") ? "?import" : "";
?>

<style>
    .menu li {
        padding:3px 0;
        font-family:'Open Sans';
    }
</style>
<ul class="<?php isset($_imported) && $_imported=="1" ? print "menu" : print "dropdown-menu"; ?>">
    <!--<li>
        <a href="{{action("BackofficeReportController@index","frontdeskDailySales$imported") }}">Room Sales</a>
    </li>-->
    
    <li>
        <a href="{{action("BackofficeReportController@index","frontofficeControl$imported") }}">Frontoffice Control</a>
     </li>


    <li>
        <a href="{{action("BackofficeReportController@index","frontofficeArrival$imported") }}">Arrival</a> 
    </li>


    <li>
        <a href="{{action("BackofficeReportController@index","frontofficeExpectedArrival$imported") }}">Expected Arrival</a> 
    </li>


    <li>
        <a href="{{action("BackofficeReportController@index","frontofficeDeparture$imported") }}">Departure</a>
     </li>

    <li>
        <a href="{{action("BackofficeReportController@index","frontofficeExpectedDeparture$imported") }}">Expected Departure</a> 
    </li>

    <li>
        <a href="{{action("BackofficeReportController@index","frontdeskServiceSales$imported") }}">Service Sales</a>
    </li>

    <li>
        <a href="{{action("BackofficeReportController@index","foDeposits$imported") }}">Cash Deposits</a>
     </li>
   
    <li><a href="{{action("BackofficeReportController@index","frontofficePayment$imported") }}">Payment Control</a> </li>
    <li><a href="{{action("BackofficeReportController@index","frontofficeBreakfast$imported") }}">Breakfast</a> </li>
    <li><a href="{{action("BackofficeReportController@index","frontdeskMorning$imported") }}">Morning Report</a> </li>
    <li><a href="{{action("BackofficeReportController@index","rooming$imported") }}">Police Report</a></li>
    <!--<li><a href="{{action("BackofficeReportController@index","banquet$imported") }}">Halls</a> </li>-->
    <li><a href="{{action("BackofficeReportController@index","banquetBooking$imported") }}">Halls Booking</a> </li>
    <li><a href="{{action("BackofficeReportController@index","roomtransfers$imported") }}">Room Transfer</a> </li>
    <!--<li><a href="#">Invoices</a> </li>-->
    <li><a href="{{action("BackofficeReportController@index","foPayments$imported") }}">Payments</a></li>
    <li><a href="{{action("BackofficeReportController@index","foLogs$imported") }}">Logs</a></li>
</ul>