@extends('/Pos/master')

@section("contents")

@if (Auth::user()->level > 3)
<script>
    $(document).ready(function(){
        $("html").css({
            "overflow":"hidden"
        });

        $(".main-container").css("overflow","auto");
    });

   
</script>
<div class="pos_box row">
    <div class="pos_title">
        <b>POS {{\Session::get("pos.mode",0)=="health_center" ? "Health Club" : "" }}</b>
        @if(\App\SalesMode::getMode()!=\App\SalesMode::RESTOv2)
        <button class="suspendedbills_btn">
            Suspended Bills (<span>0</span>) <i class="fa fa-angle-down"></i>
            <span class="arrow"></span>
        </button>
     
        <input class="pos-search-field" type='text' placeholder="Search Bill ID" />
        <button class='pos-search-btn'>
            <i class="fa fa-search"></i>
        </button>

        <ul id="suspended_bills_list"></ul>
        @endif
    </div>
    <div class="pos_biller col-md-6">
        <div class='pay_box'></div>
        <div>
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <input type="hidden" id="local_bill_id" value="-1" />
            <input type="hidden" id="server_bill_id" value="-1" />
            <div class="form-group">
                <div class="input-group customerg">
                    <span class="input-group-addon">Customer <i class="fa fa-user"></i></span>
                    <input type="text" data-target="customer-list-wrapper" class="form-control" id="customer" value="Walkin" placeholder="Customer Name" aria-describedby="basic-addon1">
                    <span style="background: rgb(233, 235, 255)" class="input-group-addon">
                        <button data-target=".customer-list-wrapper" data-toggle="dropdown" class="btn customer-btn"><i class="fa fa-users"></i></button>
                    </span>

                    <div class="customer-list-wrapper"><?php
                    $customers = \DB::select("select customerid,nickname from customers where favorite=1");
                 ?>
                        <ul>
                            @foreach($customers as $customer)
                            <li><a class="customer-item-btn" data-id="{{ $customer->customerid }}" data-name="{{ $customer->nickname }}" href="#"><i class="fa fa-user"></i>{{ $customer->nickname }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="input-group waiterg">
                    <select id="waiter" class="thechosen flat">
                        <option value="0">{{(\Session::get("pos.mode") == "health_center") ? "Serviced by" : "Choose Waiter"}}</option><?php
		   	$waiters = DB::select("select idwaiter,waiter_name from waiters where is_active=1");
		   	foreach ($waiters as $waiter):
		   	?>
                        <option value="{{$waiter->idwaiter}}">{{$waiter->waiter_name }}</option><?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon1">{{ \App\SalesMode::getMode()==\App\SalesMode::NORMAL ? "Product" : "Order"}} <i class="fa fa-search"></i></span>

                @if(\App\SalesMode::getMode()==\App\SalesMode::NORMAL)
                <input autocomplete="off" type="text" id="product_search" class="form-control" placeholder="Product name / Code" aria-describedby="basic-addon1">
                @elseif(\App\SalesMode::getMode()== \App\SalesMode::RESTOv2)
                <div style="position:relative">
                    <input autocomplete="off" type="text" id="bill_search" class="order-search-field form-control" placeholder="Enter Bill ID" aria-describedby="basic-addon1">
                </div>
                @elseif(\App\SalesMode::getMode()== \App\SalesMode::RESTO)
                <div style="position:relative">
                    <input autocomplete="off" type="text" id="order_search" class="order-search-field form-control" placeholder="Enter Order ID" aria-describedby="basic-addon1">
                </div>
                @endif
            </div>

            @if(\App\POSSettings::get("custom_product")== "1")
            <button class="custom_prod_btn"><i class="fa fa-plus"></i> Custom Product</button>
            @endif
            <div class="clearfix"></div>
            <div class="new_prod">
                <form id="custom_product_form" action="{{ action('ProductsController@CreateCustomProduct') }}" method="post">
                    <button class="new_prod_close_btn"><i class='fa fa-times'></i></button>
                    <label>Name</label> <input name="product_name" type="text">
                    <label>Price</label> <input name="product_price" type="text">
                    <label>Category</label> <select style="max-width:150px;display:block" required="" name="category">
                        <option value="">Category</option><?php $cats = \DB::select("SELECT id,category_name,store_name,store_id FROM categories join category_store on category_store.category_id = categories.id join store on store.idstore = store_id"); ?>
                        @foreach($cats as $cat)
                        <option value="{{ $cat->id }}-{{$cat->store_id}}">{{$cat->category_name}} ({{ $cat->store_name }})</option>
                        @endforeach
                    </select>
                    <input type="submit" value="Add">
                    {!! csrf_field() !!}
                </form>
            </div>
        </div>
        <table id="purchase_table">
            <tr>
                <th class='item_col'>Item</th>
                <th class="price_col">U. Price</th>
                <th class="qty_col">Qty</th>
                <th class="total_col">Total</th>
                <th class="action_col"><i class='fa fa-times'></i></th>
            </tr>

        </table>
        <div class="summary container-fluid">
            <div class="col-md-6">Tax</div>
            <div class="col-md-6"><input type="text" id="tax" value="0"></div>
            <div class="col-md-6">Total Payable</div>
            <div class="col-md-6"><input type="text" id="totalPayable" value="0"></div>
        </div>
        <div class="actions">
            <ul>
                <li><a class="suspend_btn" href=""> <i class="fa fa-pause"></i> Save</a></li>
                <li><a class="pay_btn" href=""> <i class="fa fa-money"></i> Pay</a></li>
                <li><a class="print_btn" href=""> <i class="fa fa-print"></i> Print</a></li>
                <li><a class="cancel_btn" href=""> <i class="fa fa-ban"></i> Reset</a></li>
            </ul>
        </div>
    </div>
    @if(\App\SalesMode::getMode()==\App\SalesMode::NORMAL)
    <div class="pos_products col-md-6">
        <div class="prod_filter">
            <ul>
                <li>
                    <select id="store_list" class="thechosen">
                        <option>All Stores</option><?php
               $stores = \App\Store::all();
			   	foreach ($stores as $store):
		   	?>
                        <option value="{{$store->idstore}}">{{$store->store_name }}</option><?php endforeach; ?>
                    </select>
                </li>
                <li class="filter"><a class="favorite_prod_btn" href="#"><i class="fa fa-star"></i> Favorites</a></li>
                <li>
                    <select class="thechosen">
                        <option>Choose Category</option>
                    </select>
                </li>
            </ul>
        </div>
        <p class="text-center" style="margin-top:10px"><i class="fa fa-filter"></i> Products</p>
        <div class="product_lists">
            <div class="progress">
                <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                    <span class="sr-only">45% Complete</span>
                </div>
            </div>
            <div class="recent_prods shown">
            </div>
            <ul class="pop_prods"></ul>
            <ul class="cat_prods"></ul>
        </div>
    </div>
    @elseif(\App\SalesMode::getMode()==\App\SalesMode::RESTO)
    <div class="orders-wrapper">
        <ul class="orders-list"></ul>
    </div>

    @elseif(\App\SalesMode::getMode()==\App\SalesMode::RESTOv2)
    <div class="bills-wrapper">
        <ul class="bill-list"></ul>
    </div>
    @endif
</div>

<script type="text/javascript">

    var billTotal = 0;
    var billItems = [];
    var products = {};
    var taxTotal = 0;
    var suspendedBills = [];


    $(document).ready(function(){

        /** Load Products **/

        $(".product_lists").loadProducts({url:"/POS/Products/json",store_id:0,category_id:0,favorite:true});
        /** Add Product To Purchase List **/
        $(".pos_box").billOperations({
            suspendUrl : "<?php echo action("BillsController@suspend"); ?>",
            suspendedUrl : "<?php echo action('BillsController@getSuspendedBills'); ?>",
            billUpdateUrl:"<?php echo action("BillsController@updateBill"); ?>",
            billDeleteUrl:"<?php echo action("BillsController@destroy"); ?>",
            paySuspendeBillUrl: "<?php echo action("BillsController@paySuspendedBill"); ?>",
            payBillUrl: "<?php echo action("BillsController@pay"); ?>",
            searchUrl:"<?php echo action("ProductsController@searchProduct"); ?>",
            assignBillUrl:"<?php echo action("BillsController@assignBill"); ?>",
			shareBillUrl:"{{ action('BillsController@shareBill') }}",
            checkRoomUrl : "{{action("BillsController@checkRoom") }}",
            getOrderUrl:"<?php echo action("OrdersController@getOrder"); ?>",
            autoloadBills:{{ \App\SalesMode::getMode()==\App\SalesMode::NORMAL ? 'true' : 'false' }},
            autoloadWaiterBills: {{ \App\SalesMode::getMode()==\App\SalesMode::RESTOv2 ? 'true' : 'false' }},
            taxPercent : 18
    });

    @if(\App\SalesMode::getMode()==\App\SalesMode::RESTO)
        $(".orders-list").loadOrders({
            url : "<?php echo action("OrdersController@getOrders"); ?>",
        });
    @endif

    setInterval(function(){
        $(".orders-list").loadOrders({
            url : "<?php echo action("OrdersController@getOrders"); ?>",
        });
    },40000);

    $("#store_list").change(function(){
        store =  $(this).val();
        $(".product_lists").loadProducts({url:"/POS/Products/json",store_id:store,category_id:0});
    })

    $(".favorite_prod_btn").click(function (e) {
        $(".product_lists").loadProducts({ url: "/POS/Products/json", store_id: 0, category_id: 0, favorite: true });
    })
    $(".suspendedbills_btn").click(function(e){
        $(this).children(".arrow").toggle();
        $("#suspended_bills_list").toggle(100);
    })

    $("#suspended_bills_list").on("click",".waiter_btn", function(e){
        e.preventDefault();
        $(this).children(".fa").toggleClass("fa-plus-square-o fa-minus-square-o");
        $(this).parent().children("ul").toggle();
    })

    $(".new_prod_close_btn").click(function(e){
        e.preventDefault();
        $(".new_prod").toggle();
    });

    $(".custom_prod_btn").click(function(e){
        e.preventDefault();
        $(".new_prod").toggle();
    })
    })

</script>
@else


<h2 class="text-danger text-center">Your are not allowed to access this Page</h2>

@endif
@stop