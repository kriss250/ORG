@extends('/Pos/OrderMaster')
@section("contents")
<main class="maincontainer hidden"></main>

<script>
    billOrderItems = {"billItems":[], "orderItems":[] };

    $(document).ready(function () {
        $(".waiter-login-wrapper").removeClass("hidden");
        $("body").on("click", ".cancel_login_btn", function () {
            location.href = "/Order";
        });

        $(".save_print_order").html("Show Orders").click(function () {
            var waiter = $("[name='waiterid']:checked").val();
            var pin = $("#waiter-pin-input").val();
            window.currentWaiter = waiter;
            loadTables(waiter,pin);
        });
    });

    /**
     * Load table orders
     */
    var loadTableOrders = function(elm,waiter,table){

        billOrderItems = { billItems:[], orderItems:[] };
        $(".table-list li.active").removeClass("active");
        $(elm).addClass("active");
        $(".bill-wrapper").remove();
        $.get("{{route('tableOrders')}}?waiter=" + waiter + "&table="+table,function(data){
            if(data.length > 0){
                var ordersContainer = $("<div>").addClass("orders-container");
                tblist = $(".table-list");
                $(tblist).addClass("mini-list");
                if($(".orders-container").length ==0){
                    $(ordersContainer).insertAfter($(tblist));
                }else {
                    ordersContainer = $(".orders-container");
                }

                $(ordersContainer).html("");

                $.each(data,function(k,v){
                    sorder = $("<div class='single-order'>");
                    orderTable = $("<table>").addClass("order-items-table table table-reponsive");
                    $(sorder).html($("<header>").html("Order : "+v.idorders));

                    $.each(v.items,function(c,s){

                        if(s.qty - s.billed_qty == 0) return true;
                        row = $("<tr>").attr({"store_id":s.store_id,"order-id":s.order_id,"item-id":s.idorder_items,"item-product-id":s.product.id,'item-qty':(s.qty-s.billed_qty)});
                        $(row).append($("<td width='40%'>").html(s.product.product_name));
                        $(row).append($("<td>").html(s.unit_price));
                        $(row).append($("<td>").html(s.qty));
                        $(row).append($("<td>").html($("<input name='qty_input_"+s.idorder_items+"' min='1' max='"+(s.qty-s.billed_qty)+"' value='"+(s.qty-s.billed_qty)+"' type='number'>")));
                        var actionBtn = $("<button>").html('<i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i').addClass("add-tobill-btn")
                          .click(function(){
                              if(typeof $(this).attr("disabled") == "undefined"){
                                  product = s;
                                  product.qty = parseFloat($('[name="qty_input_'+s.idorder_items+'"]').val());
                                  inputField = $('[name="qty_input_'+s.idorder_items+'"]');
                                  totalQty = parseFloat($(this).parent().parent("tr").attr("item-qty"));


                                  ExistingOrderItem = billOrderItems.orderItems.filter(function(obj){
                                      return obj.order == s.order_id && obj.product==s.product_id
                                  });


                                  onBillQty = ExistingOrderItem.length ==0 ? 0 :  ExistingOrderItem[0].qty;
                                  leftQty  = totalQty-product.qty- (typeof onBillQty=="undefined" ? 0 : onBillQty );

                                  $(inputField).attr("max",leftQty).val(leftQty);
                                  addToBill(product);

                                  if(leftQty==0)
                                  {
                                      $(this).attr("disabled","disabled");
                                      $(this).parent().parent("tr").addClass("locked-row");
                                  }

                              }
                          });
                        $(row).append($("<td>").html(actionBtn));
                        $(orderTable).append(row);
                    });
                    $(sorder).append(orderTable);
                    $(ordersContainer).append(sorder);
                });
            }else {  $(".orders-container").html("");}
        });
    };

    /**
     * Update total on the bill
     */
    var updateTotalLabel = function(){
        var total = 0;

        $.each(billOrderItems.billItems,function(k,v){
            total += v.price*v.qty;
        });

        if(parseFloat($("[name='discount-amount']").val()) > 0){

            if($("[name='discount-type']:checked").val()== "percent"){
                total -= (total*parseFloat($("[name='discount-amount']").val()))/100;
            }else {
                total -= parseFloat($("[name='discount-amount']").val());
            }
        }
        $(".counter").html(total);
    };

    /**
     * Add Item to the bill
     */
    var addToBill = function(item){
        addToObject(item);

        var bill = $(".bill-wrapper");
        var table = $(".bill-wrapper table");

        if(table.length == 0){
            table = $("<table>");
        }

        var row =$(".bill-wrapper tr[item-product-id="+item.product.id+"]");

        if($(row).length < 1)
        {
            //Create a new row
            row  = $("<tr>").attr("item-product-id",item.product.id);
            $(row).append($("<td width='40%'>").html(item.product.product_name));
            $(row).append($("<td>").html(item.unit_price));
            $(row).append($("<td>").html(item.qty));
            var delBtn = $("<button>").addClass("i-del-btn").html('<i class="fa fa-trash"></i>').attr("onclick",'removeItemFromBill(this)')
            $(row).append($("<td class='text-right'>").html(delBtn));
            $(table).append(row);
        }else {
            //uppdate
            existingTd =  $(row).children("td");
            var existing = $(existingTd[2]).html();
            $(existingTd[2]).html(parseFloat(existing)+item.qty);
        }



        if(bill.length == 0)
        {
            //Create bill container
            bill = $("<div class='bill-wrapper'>");
            $(bill).html("<h3>New Bill</h3>");

            var saveBtn = $("<button>").attr("onclick","saveBill()").addClass("btn btn-primary btn-lg save-btn").html("Save & Print");
            var discountToggle = $("<button>").addClass('discount-toggle btn btn-xs btn-default').html('+ DISCOUNT %').click(function(){
                $(".discount-box").toggleClass("hidden");
            });

            var counter = $("<div class='counter'>").html("0");

            $(bill).append(table);

            $(bill).append(discountToggle);

            $(bill).append(createDiscountBox());

            $(bill).append(counter).append(saveBtn);

            $(bill).insertAfter($(".orders-container"));

        }

        updateTotalLabel();
    };

    /**
     * Create discount Box
     */
    var createDiscountBox= function(){
        var discountBox = $("<div class='discount-box hidden'>");
        var input = $("<input value='0' type='text' name='discount-amount'>");
        var radio1 = $("<input type='radio' value='percent' class='form-control' checked name='discount-type'>");
        var radio2 = $("<input type='radio' value='fixed' class='form-control' name='discount-type'>");
        var group = $("<div class='input-group col-sm-5'>");
        var addon = $("<span class='input-group-addon'>");
        $(input).keyup(function(){
            updateTotalLabel();
        });

        $(radio1).click(function(){updateTotalLabel();});
        $(radio2).click(function(){updateTotalLabel();});
        var group2 = $(group).clone();
        $(group).addClass("pull-left").append($("<span class='input-group-addon'>").html("Percent % ")).append(radio1);
        $(group2).addClass("pull-right").append($("<span class='input-group-addon'>").html("Fixed ")).append(radio2);

        return $(discountBox).append("<label>Discount</label>")
                 .append(input)
                 .append(group).append(group2);

    };

    /**
     * Add item to object
     */
    var addToObject  = function(item){

        if(billOrderItems.billItems.length  == 0 ){
            billOrderItems.billItems.push({id:item.product_id, qty:item.qty,price:item.unit_price,store_id:item.store_id });
        }else {
            //Search
            ExistingbillItem = billOrderItems.billItems.filter(function(obj){
                return obj.id == item.product_id
            });

            if(ExistingbillItem.length > 0){
                i = billOrderItems.billItems.indexOf(ExistingbillItem[0]);
                billOrderItems.billItems[i].qty += item.qty;
            }else {
                billOrderItems.billItems.push({id:item.product_id, qty:item.qty,price:item.unit_price });
            }

        }


        //Order
        if( billOrderItems.orderItems.length  == 0 ){
            billOrderItems.orderItems.push({order:item.order_id,product:item.product_id,qty:item.qty,itemid:item.idorder_items});
        }else {
            //Search
            ExistingOrderItem = billOrderItems.orderItems.filter(function(obj){

                return obj.order == item.order_id && obj.product==item.product_id
            });

            if(ExistingOrderItem.length ==0 ) {
                billOrderItems.orderItems.push({order:item.order_id,product:item.product_id,qty:item.qty,itemid:item.idorder_items});
            }else {
                i = billOrderItems.orderItems.indexOf(ExistingOrderItem[0]);
                billOrderItems.orderItems[i].qty += item.qty;
            }
        }



    }

    /**
     * Remove Product from object
     */
    var removeFromObject = function(product_id){
        try {
            billitem = billOrderItems.billItems.filter(function(obj){
                return obj.id == product_id
            });
            orderitems = billOrderItems.orderItems.filter(function(obj){
                return obj.product == product_id
            });
            $.each(orderitems,function(k,v){
                i = billOrderItems.orderItems.indexOf(v);
                billOrderItems.orderItems.splice(i,1);
            });
            index = billOrderItems.billItems.indexOf(billitem[0]);
            billOrderItems.billItems.splice(index,1);
        }catch(ex){
            alert(ex);
        }

        updateTotalLabel();
    }

    /**
     * Delete order from object
     */
    var removeFromOrderObject = function(product_id,order_id)
    {
        try {
            orderitem = billOrderItems.orderItems.filter(function(obj){
                return obj.id == product_id && obj.order ==order_id
            });
            index = billOrderItems.orderItems.indexOf(orderitem[0]);
            billOrderItems.orderItems.splice(index,1);
        }catch(ex){
            alert(ex);
        }

    }

    /**
     * Save The Bill
     */
    var saveBill = function(waiter){

        $(".waiter-login-list").hide();
        $("#waiter-pin-input").val("");
        $(".waiter-login .pull-right").css({"width":"100%","float":"none"});
        width = $(".waiter-login .pull-right").width();
        $(".waiter-login").css("padding","26px 43px").width(width+160);
        $(".save_print_order").hide();
        $(".cancel_login_btn").click(function(e){
            e.stopPropagation();
            $(".waiter-login-wrapper").addClass("hidden");
            return true;

        });

        $(".waiter-login-wrapper").removeClass("hidden");
        bbtn = $('.order_bill_print').length > 0 ? $('.order_bill_print') : $("<button class='push-btn btn btn-success order_bill_print'>");
        $(bbtn)
        .html("Save & Print")
        .click(function(e){
            e.preventDefault();
            e.stopPropagation();
            elm = $(this);
            if(typeof $(this).attr("disabled") !=="undefined"){
                return;
            }
            $(this).attr("disabled","disabled");
            pin  =  $("#waiter-pin-input").val();

            var waiter = {id:window.currentWaiter,pin:$("#waiter-pin-input").val()};
            var discount = {type:$("[name='discount-type']:checked").val(),value: parseFloat( $("[name='discount-amount']").val())};

            data = {data:JSON.stringify(billOrderItems),_token:'{{csrf_token()}}',waiter,discount};

            $.post('{{route('saveWaiterBill')}}',data,function(data){
                if(typeof data.success !== "undefined" && data.success == "1" ){
                    billOrderItems = {"billItems":[], "orderItems":[] };
                    window.printBill(data.bill,{waiter:waiter});
                    $(elm).removeAttr("disabled");
                    $(".bill-wrapper").remove();
                    $(".waiter-login-wrapper").addClass("hidden");
                    $(".save_print_order").show();

                }else {

                    alert(typeof data.error ==="undefined" ? "ERROR SAVING BILL" : data.error);
                    return false;
                }
            }).complete(function(){ $(elm).removeAttr("disabled");});
        }).appendTo($(".waiter-login-btns .pull-left"));
    }

    /**
     * Remove Product From bill
     */
    var removeItemFromBill = function(elm){

        var theRow = $(elm).parent().parent("tr");
        code = $(theRow).attr("item-product-id");

        similar = $(".orders-container [item-product-id="+code+"]");

        $(theRow).remove();
        $.each(similar,function(x,y){
            maxQty = parseFloat( $(y).attr("item-qty"));
            $(y).removeClass("locked-row");
            $(y).find(".add-tobill-btn").removeAttr("disabled");
            inputs = $(y).find("input");
            $(inputs[0]).val($(y).attr("item-qty")).attr("max",maxQty);

        });
        removeFromObject(code);
    };

    /*
     * Load Tables
     */
    var loadTables = function (waiter,pin) {
        token = $("[name='csrf-token']").attr("content");
        $.post("{{route('myTables')}}",{waiter: waiter,pin:pin,_token : token}, function (data) {
            if (typeof data.error !== "undefined") {
                alert(data.error);
                return;
            } else {
                $(".waiter-login-wrapper").addClass("hidden");

                ul = $("<ul>");

                $.each(data, function (k, b) {
                    $(ul).addClass("table-list")
                        .append($("<li>")
                        .attr("onclick","loadTableOrders(this,"+waiter+","+b.table.idtables+")")
                        .attr("data-table",b.table.idtables)
                        .html(b.table.table_name));
                });


                $(".maincontainer").removeClass("hidden").html(ul);
            }
        });
    };


</script>

@stop