@extends('/Pos/OrderMaster')

@section("contents")

<style>
    .main-container {
        padding: 0 15px !important;
        border-radius: 0 !important;
    }
</style>
<script>
    $(document).ready(function(){
        var screenHeight = $(window).height();

        $("html").css({
            "overflow":"hidden"
        });

        $(".main-container").css("overflow","auto");

        $("#purchase_table").css({
            "height":(screenHeight*0.54)+"px",
            "max-height":(screenHeight*0.55)+"px"
        });

        $(".product_lists").css({
            "height":(screenHeight*0.77)+"px",
            "max-height":(screenHeight*0.8)+"px"
        });

    });
</script>
<div class="pos_box row">

    <div class="pos_biller col-md-5">

        <div class='pay_box'></div>

        <div>

            <input type="hidden" name="_token" value="{{ csrf_token() }}" />

            <input type="hidden" id="local_bill_id" value="-1" />
            <input type="hidden" id="server_bill_id" value="-1" />

            <div class="form-group customer-row">
                <div class="input-group customerg">
                    <span class="input-group-addon">Customer <i class="fa fa-user"></i></span>
                    <input type="text" data-target="customer-list-wrapper" class="form-control" id="customer" value="Walkin" placeholder="Customer Name" aria-describedby="basic-addon1">
                    <span style="background: rgb(233, 235, 255)" class="input-group-addon">
                        <button data-target=".customer-list-wrapper" data-toggle="dropdown" class="btn customer-btn"><i class="fa fa-users"></i></button>
                    </span>

                    <div class="customer-list-wrapper">
                        <?php
                    $customers = \DB::select("select customerid,nickname from customers where favorite=1");
                        ?>
                        <ul>
                            @foreach($customers as $customer)
                            <li><a class="customer-item-btn" data-id="{{ $customer->customerid }}" data-name="{{ $customer->nickname }}" href="#"><i class="fa fa-user"></i>{{ $customer->nickname }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>



            </div>

            <div class="input-group product-row">
                <span class="input-group-addon" id="basic-addon1">Product <i class="fa fa-search"></i></span>
                <input type="text" id="product_search" class="form-control" placeholder="Product name / Code" aria-describedby="basic-addon1">
            </div>
            <!--<button class="custom_prod_btn"><i class="fa fa-plus"></i> Custom Product</button>-->
            <div class="clearfix"></div>
            <div class="new_prod">
                <form id="custom_product_form" action="{{ action('ProductsController@CreateCustomProduct') }}" method="post">
                    <button class="new_prod_close_btn"><i class='fa fa-times'></i></button>
                    <label>Name</label> <input name="product_name" type="text">
                    <label>Price</label> <input name="product_price" type="text">
                    <label>Category</label> <select style="max-width:150px;display:block" required="" name="category">
                        <option value="">Category</option>
                        <?php $cats = \DB::select("SELECT id,category_name,store_name,store_id FROM categories join category_store on category_store.category_id = categories.id join store on store.idstore = store_id"); ?>
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

    <div class="pos_products col-md-7">
        <div class="prod_filter">
            <ul class="cat-list">
                @foreach(\App\Category::all() as $cat)
                <li>
                    <button>
                        <i class="fa fa-sitemap"></i>
                        {{    $cat->category_name}}
                    </button>
                </li>
                @endforeach
            </ul>
        </div>

        <p class="text-center" style="margin-top:10px">
            <ul class="alpha-list">
                <li class="active">A</li>
                <li>B</li>
                <li>C</li>
                <li>D</li>
                <li>E</li>
                <li>F</li>
                <li>G</li>
                <li>H</li>
                <li>I</li>
                <li>K</li>
                <li>L</li>
                <li>M</li>
                <li>N</li>
                <li>O</li>
                <li>P</li>
                <li>Q</li>
                <li>R</li>
                <li>S</li>
                <li>T</li>
                <li>V</li>
                <li>X</li>
                <li>Y</li>
                <li>Z</li>
</ul>
        </p>

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
        $(".pos_box").OrderOperation({
            suspendUrl : "<?php echo action("BillsController@suspend"); ?>",
            searchUrl:"<?php echo action("ProductsController@searchProduct"); ?>",
			taxPercent : 18
		});

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
@stop