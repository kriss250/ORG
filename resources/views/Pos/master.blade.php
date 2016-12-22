<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" >

        {!! HTML::style('assets/css/vendor/bootstrap.min.css') !!}
        {!! HTML::style('assets/css/vendor/font-awesome.min.css') !!}
        {!! HTML::style('assets/js/vendor/chosen/chosen.css') !!}
        {!! HTML::style('assets/js/vendor/datatables/css/jquery.dataTables.min.css') !!}
        {!! HTML::style('assets/js/vendor/datatables/extensions/TableTools/css/dataTables.tableTools.min.css') !!}
        {!! HTML::style('assets/js/vendor/datepicker/css/bootstrap-datepicker3.standalone.min.css') !!}
        {!! HTML::style('assets/css/vendor/jquery-ui.min.css') !!}
        {!! HTML::style('assets/js/vendor/datetimepicker/css/bootstrap-datetimepicker.min.css') !!}
        {!!HTML::style('assets/css/POS.css') !!}

        {!!HTML::style('assets/css/'.(\Session::get("pos.mode")).'.css')!!}

    <!-- SCRIPTS -->
        {!! HTML::script('assets/js/vendor/jquery/jquery-1.11.2.min.js') !!}
        {!! HTML::script('assets/js/vendor/bootstrap/bootstrap.min.js') !!}
        {!! HTML::script('assets/js/vendor/jquery-ui/jquery-ui.min.js') !!}
        {!! HTML::script('assets/js/vendor/moment/moment.min.js') !!}
        {!! HTML::script('assets/js/vendor/datatables/js/jquery.dataTables.min.js') !!}
        {!! HTML::script('assets/js/vendor/chosen/chosen.jquery.min.js') !!}
        {!! HTML::script('assets/js/vendor/datatables/extensions/TableTools/js/dataTables.tableTools.min.js') !!}
        {!! HTML::script('assets/js/vendor/accounting/accounting.js') !!}
        {!! HTML::script('assets/js/vendor/datetimepicker/js/bootstrap-datetimepicker.min.js') !!}
        {!! HTML::script('assets/js/vendor/datepicker/js/bootstrap-datepicker.min.js') !!}
        {!! HTML::script('assets/js/pos.jquery.js') !!}
        {!! HTML::script('assets/js/POS.js') !!}


    <title>ORG POS </title>
</head>
<body class="noselect"> <div class="print_container"></div>
<?php 

if(isset($_GET['store_switch']))
{
    \Auth::user()->wstore = $_GET['store_switch'];
    \Auth::user()->save();
}

if(isset($_GET['working_stores']))
{
    \Session::put('working_stores',explode(',',$_GET['working_stores']));
    setcookie('working_stores',serialize(explode(',',$_GET['working_stores'])),time()+86400*90);

}


?>

@if(isset($errors) && count($errors) > 0)
        <div style="background: rgb(192, 57, 43) none repeat scroll 0% 0%; color: rgb(0, 0, 0);" class="ualert ui-draggable ui-draggable-handle"><i class="fa fa-exclamation-triangle"></i><div class="inner_content">

        {{  $errors->first() }}
        </div><button class="ok-btn ht_close">OK</button></div>
@endif


<script type="text/javascript">

$(document).ready(function(){

    $(".report-print-btn").click(function (e) {
        e.preventDefault();
        var headerDiv = $("<div class='print-header'>");
        var footerDiv = $("<div class='print-footer'>");
        var ds = "";
        var title = $(this).attr("data-title");
        var dates = $(this).attr("data-dates");
        $.ajax({
            url: "{{ url('/Backoffice/Reports/Header') }}?title=" + title + "&date_range=" + dates,
            type: "get",
            success: function (data) {
                if ($(".print-header").length == 0)
                {
                    $(".print-header").html("");
                }
                $(headerDiv).html(data);
                $(".contents").prepend($(headerDiv));
                $(".contents").addClass("print-area");
                $(".contents").css("display", "block !important");
                $(footerDiv).html("Printed By {{ \Auth::user()->username }}");
                $(".contents").append($(footerDiv));
                $("body").addClass("printable-body");
                window.print();
                $(".print-header").remove();
                $(".print-footer").remove();

            }
        })

    })

    $(".fullscreen-switch").click(function(e){
            e.preventDefault();
            $("body").toggleClass("fullscreen");
        })

        $(".ht_close").click(function(e){
            e.preventDefault();

            $(this).parents(".ualert").fadeOut(200,function(){
                $(this).remove();
            })
        })

        $(".theme-switch").change(function(){
            if($(this).val().length>1)
            {
                $("head #theme").remove();
                $("head").append($(this).val());
            }else {
                $("head #theme").remove();
            }

        })
        $("#change_pwd_btn").click(function(e){
            e.preventDefault();
        });
});

var countSales  = function()
{
    $(document).ready(function(){

        $.ajax({
            url : "{{action('BillsController@getCurrentSales') }}",
            type: "get",
            success : function (data){
              try{
                data = JSON.parse(data);
                $(".sales_counter").children("b").html(data.cash);
                $(".card_sales_counter").children("b").html(data.card);
                var cash =accounting.unformat(data.cash);
                var card = accounting.unformat(data.card);
                var total = cash+card;
                $(".total_sales_counter").children("b").html(accounting.formatMoney(total,"",0));
              }catch(e)
              {

              }


            },
            error : function(){

            }
        })

    })

}

countSales();

function switchStore(src)
{
        $(src).parent("form").submit();
}
</script>

<div class="header">
<div class="grid">
<div class="row">
        <div class="col-md-3 col-xs-3">
             <h2 class="pos_logo">ORG POS <i class="fa fa-bars"></i></h2>
        </div>

        <div class="col-md-9 col-xs-9 header-right">

          <ul>
          <li>
          <span class="theme-switch-wrapper">
              <form method="get" action="">
                  <select onchange="switchStore(this);" name="store_switch" class="store-switch">
                      <option value="0">All Stores</option>
                      @foreach (\App\Store::all() as $store)
                      <option {{\Auth::user()->wstore == $store->idstore ? "selected " : ""  }} value="{{$store->idstore}}">{{$store->store_name}}</option>
                      @endforeach
                  </select>
                  <i class="fa fa-angle-down"></i>
              </form>
          </span>
          </li>

                   <li>
          <span class="theme-switch-wrapper">
              <select class="theme-switch">
                  <option value="">Default</option>
                  <option value='{!! HTML::style('assets/css/dark_violet-theme.css',["id"=>"theme"]) !!}'>Dark Violet</option>
                  <option value='{!! HTML::style('assets/css/dark_blue-theme.css',["id"=>"theme"]) !!}'>Dark Blue</option>
              </select>
            <i class="fa fa-angle-down"></i>
          </span>
          </li>

          <li>
 <a href="#" class="btn btn-xs btn-success fullscreen-switch"><i class="fa fa-arrows-alt"></i>
</a></li>
          </li>

<li>
                <a href="{{ route('pos') }}" class="btn btn-xs btn-danger">POS</a></li>

<li>

<span class="clock"><i class="fa fa-clock-o"></i> {{ date('l d, m Y',strtotime(\ORG\Dates::$RESTODT)) }} <i class="time">{{ date('H:i') }}</i></span>
</li>

<li>
                <div class="btn-group">
                  <button type="button" class="btn btn-default dropdown-toggle login-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Hi, {{ Auth::user()->username }} ! <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a href="{{ url('/POS/Settings/changePassword') }}">Change Password</a></li>
                    <li><a href="{{ route('logout') }}">Logout</a></li>
                  </ul>
                </div>

                </li>


            </ul>
        </div>
     </div>

</div>
</div>

<div class="grid main-container">
<div class="row">
    <div class="mini-submenu">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </div>

    <div class="col-md-3 hidden-xs sidebar">
     <nav class="list-group">
       <span href="#" class="list-group-item active">
            Menu
            <span class="pull-right" id="slide-submenu">
                <i class="fa fa-times"></i>
            </span>
        </span>

       <ul>
         <li class="list-group-item"><a href="{{ route('pos') }}"><i class="fa fa-home"></i> Home</a></li>

         @if(Auth::user()->level ==10)
         <li class="list-group-item dropdown"><a href=""> <i class="fa fa-archive"></i> Products </a>
         <span><i class="fa fa-angle-down"></i></span>
            <ul class="dropdown_menu">
            <li><a href="{{ action('StoreController@create') }}">New Store</a></li>
              <li><a href="{{ action('ProductsController@create') }}">New Product</a></li>
              <li><a href="{{ action('ProductsCategoryController@create') }}">New Category</a></li>
              <li><a href="{{ action('ProductsSubCategoryController@create') }}">New Subcategory</a></li>
              <li><a href="{{ action('ProductsController@index') }}">Product List</a></li>
                <li><a href="{{ action('ProductsController@categoryStore') }}">Category to Store</a></li>
                <li><a href="{{ action('ProductsController@productPrice') }}">Product Prices</a></li>
              <li><a href="{{ action('ProductsCategoryController@index') }}">Category List</a></li>
              <li><a href="{{ action('ProductsSubCategoryController@index') }}">SubCategory List</a></li>
            </ul>
         </li>
        @endif

         <li class="list-group-item"> <a href="{{ action('BillsController@index') }}"><i class="fa fa-file-text"></i> Bills</a></li>
        <li class="list-group-item"> <a href="{{ action('BillsController@assignedList') }}"><i class="fa fa-reply-all"></i> Assigned Bills</a></li>

         @if(Auth::user()->level ==10)
             <li class="list-group-item dropdown"><a href="#"><i class="fa fa-users"></i> People</a>
             <span><i class="fa fa-angle-down"></i></span>
                 <ul class="dropdown_menu">
                    <li><a href="{{ action('UsersController@create') }}">New User</a></li>
                    <li><a href="{{ action('CustomersController@create') }}">New Customer</a></li>
                    <li><a href="{{ action('WaiterController@create') }}">New Waiter</a></li>
                    <li><a href="{{ action('UsersController@index') }}">User Lists</a></li>
                    <li><a href="{{ action('WaiterController@index') }}">Waiter List</a></li>
                 </ul>
             </li>



         <li class="list-group-item dropdown"> <a href="#"><i class="fa fa-cog"></i> Settings</a>
         <span><i class="fa fa-angle-down"></i></span>
              <ul class="dropdown_menu">
                 <li><a href="{{ action('SettingsController@create') }}">General</a></li>
                 <li><a href="{{ url('/POS/Settings/changePassword') }}">Change Password</a></li>
              </ul>
         </li>
         @endif

         <li class="list-group-item"> <a style="color:#C81313" onclick="confirmNewDay(this);" data-destination="<?php echo action("SettingsController@newDay"); ?>"><i class="fa fa-calendar"></i> New Day</a></li>
         <li class="list-group-item dropdown"> <a href=""><i class="fa fa-files-o"></i> Reports</a>

         <span><i class="fa fa-angle-down"></i></span>
             <ul class="dropdown_menu">
                <!--<li><a href="{{ route('POSReports','summaryDay') }}">Sales Report</a></li>-->
                 <li><a href="{{route('POSReports','MyShiftReport') }}">My Shift Report</a></li>
                 <li><a href="{{ route('POSReports','DailySalesMix') }}">Sales Report</a></li>
                 <li><a href="{{ route('POSReports','RoomPost') }}">Room Posts</a></li>
                 <li><a href="{{ route('POSReports','Credits') }}">Credit</a></li>
                  <li><a href="{{ route("POSReports",'Cashier') }}">Cashier Report</a></li>
                 <li><a href="{{route('POSReports','CashierShift') }}">Shift Report(Summary)</a></li>

              </ul>
         </li>
       </ul>
     </nav>

    @if(\Session::get("pos.mode")=="default")
    <div class="notification_box" style="margin-bottom: 15px;">
    <p class="text-center">CASH COUNT <i class="fa fa-money"></i></p>
      <span class="text-center sales_counter" style="color:#2095B4;font-size:18px;"><b>0</b> <i style='font-size:10px;font-style:normal'>RWF</i></span>
    </div>


   <div class="notification_box" style="margin-bottom: 15px;">
    <p class="text-center">CARDS COUNT <i class="fa fa-credit-card"></i></p>
      <span class="text-center card_sales_counter" style="color:#2095B4;font-size:18px;"><b>0</b> <i style='font-size:10px;font-style:normal'>RWF</i></span>
    </div>

    <div class="clearfix"></div>

    <div class="notification_box" style="margin-bottom: 15px;width:100%">
    <p class="text-center">CARDS & CASH </p>
      <span class="text-center total_sales_counter" style="color:#2095B4;font-size:18px;"><b>0</b> <i style='font-size:10px;font-style:normal'>RWF</i></span>
    </div>
    @endif
    </div>

    <div class="contents col-md-9 col-lg-9">
        @yield("printHeader")
        @yield("contents")


    </div>
</div>
</div>


<div class="grid footer">
    <p class="text-center" style="margin-bottom:0px">
      ORG Point of Sale part of ORG Software Suite
    </p>
    <p class="text-center">&copy; 2015 KLAXYCOM </p>
</div>


</body>
</html>
