<ul class="dropdown-menu">
    <li><a href="{{ action("BackofficeReportController@index",'fullDay') }}">Day Report(Full)</a> </li>
    <li><a href="{{ action("BackofficeReportController@index",'summaryDay') }}">Day Report(Summary)</a> </li>
    <li><a href="{{ action("BackofficeReportController@index",'dailySales') }}">Daily Sales</a> </li>
    <li><a href="{{ action("BackofficeReportController@index",'cashierShift') }}">Cashier Shift</a> </li>
    <li><a href="{{ action("BackofficeReportController@index",'cashierBills') }}">Cashier Bills</a> </li>
    <li><a href="{{ action("BackofficeReportController@index",'productSales') }}">Products Sales(Control)</a> </li>
    
    <li><a href="{{ action("BackofficeReportController@index",'offtariffBills') }}">Off Tarriff (Free Co.)</a> </li>
    <li><a href="{{ action("BackofficeReportController@index",'roomPosts') }}">Room Posts</a> </li>
    <li><a href="{{ action("BackofficeReportController@index",'debts') }}">Debts</a> </li>
    <li><a href="{{ action("BackofficeReportController@index",'cancelledBills') }}">Cancelled Bills</a> </li>
    <li><a href="{{ action("BackofficeReportController@index",'reprintedBills') }}">Reprinted Bills</a> </li>
</ul>