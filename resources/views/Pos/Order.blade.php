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
            "height":(screenHeight*0.49)+"px",
            "max-height":(screenHeight*0.53)+"px"
        });

        $(".product_lists").css({
            "height":(screenHeight*0.77)+"px",
            "max-height":(screenHeight*0.8)+"px"
        });

        $(".waiter-login").css({
            "height": (screenHeight * 0.85) + "px",
            "max-height": (screenHeight * 0.85) + "px"
        });

        $(".waiter-login-list").css({
            "height": (screenHeight * 0.72) + "px",
            "max-height": (screenHeight * 0.85) + "px"
        });

        $(".right-scroll-cat").click(function () {
            if ($(".cat-list li:last-child").position().left+100 < $(".cat-list-wrapper").width() ) return;
            var currentMargin = $(".cat-list").css("margin-left");
            var margin = parseInt(currentMargin);
            
            var newMargin = Math.abs(margin)+80;
            newMargin =  - newMargin;
            $(".cat-list").animate({ "margin-left": newMargin+"px" }, 100)
        });

     $(".left-scroll-cat").click(function () {
            var currentMargin = $(".cat-list").css("margin-left");
            var margin = parseInt(currentMargin);
            var newMargin = parseInt(currentMargin)+80;
            newMargin = margin >= 0 ?  0 :  newMargin;

            $(".cat-list").animate({ "margin-left": newMargin+"px" }, 100)
     });

     $(".place_order_btn").click(function (e) {
         e.preventDefault();
         $(".waiter-login-wrapper").toggleClass("hidden");
     });

  
    });
</script>
<div class="pos_box row">

    <div class="pos_biller col-lg-5 col-md-6">

        <div class='pay_box'></div>

        <div>

            <input type="hidden" name="_token" value="{{ csrf_token() }}" />

            <input type="hidden" id="local_bill_id" value="-1" />
            <input type="hidden" id="server_bill_id" value="-1" />

            <div class="form-group customer-ro">
               <?php
                        $tbs = \App\Table::all();
                        $sts = \App\Store::all();
                    ?>
                <p style="margin-bottom:-4px">&nbsp;</p>
                <div class="col-xs-6">
                    <select class="form-control thechosen" id="table" name="table">
                        <option value="">Choose Table</option>
                        @foreach($tbs as $tb)
                        <option value="{{ $tb->idtables }}">{{ $tb->table_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-6">
                    <select class="form-control thechosen" id="store" name="store">
                        <option value="">Choose Store </option>
                        @foreach($sts as $st)
                        <option value="{{ $st->idstore }}">{{    $st->store_name }}</option>
                        @endforeach
                    </select>
                </div>
                </div>

            <div class="input-group product-row">
                <span class="input-group-addon" id="basic-addon1">Product <i class="fa fa-search"></i></span>
                <input type="text" id="product_search" class="form-control" placeholder="Product name / Code" aria-describedby="basic-addon1">
            </div>
            <!--<button class="custom_prod_btn"><i class="fa fa-plus"></i> Custom Product</button>-->
            <div class="clearfix"></div>
       

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
            <div class="col-md-6"><input readonly type="text" id="tax" value="0"></div>

            <div class="col-md-6">Total Payable</div>
            <div class="col-md-6"><input readonly type="text" id="totalPayable" value="0"></div>
        </div>

        <div class="actions">
            <ul>
                <li><a class="place_order_btn  btn-success" href="#"> <i class="fa fa-disk"></i> Place Order</a></li>
                <li><a class="order_cancel_btn cancel_btn" href="#"> <i class="fa fa-ban"></i> Reset</a></li>
            </ul>
        </div>

    </div>

    <div class="pos_products col-lg-7 col-md-6">
        <div class="prod_filter">
            <button class="left-scroll-cat scroll-left-btn col-md-1">
                <i class="fa fa-angle-double-left" aria-hidden="true"></i>
            </button>
            <div class="col-md-10" style="overflow:hidden">
                <div class="btn-group col-md-12 cat-list-wrapper" style="overflow:hidden;padding:0" data-toggle="buttons">
                    <ul class="cat-list">

                        <li>
                            <label class="btn btn-default active">
                                <i class="fa fa-sitemap"></i>
                                <input type="radio" name="cats" value="0" checked id="option0" autocomplete="off"> All
                            </label>
                        </li>

                        <?php $x  = 1; ?>
                        @foreach(\App\Category::all() as $cat)
                        <li>
                            <label class="btn btn-default">
                                <i class="fa fa-sitemap"></i>
                                <input type="radio" name="cats" value="{{$cat->id}}" id="option{{$x}}" autocomplete="off"> {{$cat->category_name}}
                            </label>
                        </li>
                        <?php $x++; ?>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button class="right-scroll-cat scroll-right-btn col-md-1">
                <i class="fa fa-angle-double-right" aria-hidden="true"></i>
            </button>
            </div>

        <p class="text-center" style="margin-top:10px">
            <div class="btn-group container-fluid" data-toggle="buttons">
                <ul class="alpha-list">
                    <li>
                        <label class="btn btn-default active">
                            <input checked type="radio" name="alphas" value="*" id="option_xc" autocomplete="off"> *
                        </label>
                    </li>

                    <?php foreach(range('a','z') as $i) :  ?>
                    <li>
                        <label class="btn btn-default">
                            <input type="radio" name="alphas" id="option_{{$i}}" value="{{$i}}" autocomplete="off"> {{$i}}
                        </label>
                    </li>
                    <?php endforeach; ?>

                </ul>
            </div>
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

        $(".product_lists").loadProducts({url:"/POS/Products/json",store_id:0,category_id:0,favorite:false,letter:"*"});
        /** Add Product To Purchase List **/
        $(".pos_box").OrderOperation({
            saveOrderUrl : "<?php echo action("OrdersController@saveOrder"); ?>",
            searchUrl:"<?php echo action("ProductsController@searchProduct"); ?>",
            taxPercent: 18,
            sideOrders :{!! \App\SideDish::all()->pluck("name")->toJson() !!}
		});

        $("#store_list").change(function () {
            store = $(this).val();
            $(".product_lists").loadProducts({ url: "/POS/Products/json", store_id: store, category_id: 0 });
        });


        $(".alpha-list li").click(function () {
            setTimeout(function () {
                $(".product_lists").loadProducts({
                    url: "/POS/Products/json", store_id: 0, category_id: $("[name=cats]:checked").val(), favorite: false, "letter": $("[name=alphas]:checked").val()
                });
            },100);
        });

        $(".cat-list li").click(function () {
            setTimeout(function () {
                $(".product_lists").loadProducts({
                    url: "/POS/Products/json", store_id: 0, category_id: $("[name=cats]:checked").val(), favorite: false, "letter": $("[name=alphas]:checked").val()
                });
            }, 100);
        });

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