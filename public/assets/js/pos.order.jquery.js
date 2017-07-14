(function ($) {
    $.fn.loadOrders = function (options) {
        $.extend({
            url: "",
            table_id: 0,
            waiter_id: 0
        }, options);
        var elm = $(this);

        $.ajax({
            url: options.url,
            type: "get",
            "Content-Type": "json",
            success: function (data) {
                try {
                    //data = JSON.parse(data);
                    $(elm).html("");
                    $.each(data, function (e, v) {
                        var li = $("<li class='orders-list-item'>");
                        $(li).attr("data-order_id", v.idorders).html("<b>" + v.idorders + "</b><i>" + v.waiter.waiter_name + "</i>");
                        $(elm).append(li)
                    });
                } catch (ex) {
                    alert(ex);
                }
            },
        });
    };

    $.fn.OrderOperation = function (options) {

        var billTotal = 0;
        var billItems = [];
        var products = {};
        var taxTotal = 0;
        var DBUpdate = {
            toDelete: [],
            toUpdate: [],
            newItems: []
        };

        var updateMode = false;

        var suspendedBills = []; // { idbills:, bill_total:, tax_total:, customer:, waiter_name:, waiter_id: 1, date:}
        var billTotalField = $("#totalPayable");
        var taxField = $("#tax");
        var table = $("#purchase_table");
        var customerField = $("#customer");
        var waiterField = $("#waiter");
        var elm = this;
        var prod_list = $(".product_lists");
        var updateBtn = $("<a href='#' class='update_btn'>").html("Update");
        var buttonsContainer = $(".pos_biller .actions > ul");
        var searchTextBox = $("#product_search");
        var resultsDiv = $("<div class='search_results'>").prepend("<i class='search_close_btn fa fa-times'>");
        var resultsList = $("<ul>");

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() }
        });


        $(".pos_biller").on("click", ".sd-order_btn", function (e) {
            e.preventDefault();
            var pid = parseInt($(this).parent().parent("tr").attr("data-prodid"));
            var oid = parseInt($(this).parent().parent("tr").attr("data-oid"));
            var saveBtn = $("<button class='btn btn-primary'>");
            pauseBiller();
            var sideOrdersWrapper = $("<div class='side-orders-wrapper'>");
            $(sideOrdersWrapper).html("<p>SIDE ORDERS</p>")
            
            var sideOrdersList = $("<ul>");

            $.each(options.sideOrders, function (k, v) {
                $(sideOrdersList).append("<li><label><input type='checkbox' name='side-order-input' value='"+v+"' /><span>" + v + "</span></label></li>");
            });

            $(saveBtn).html("SAVE");
            $(saveBtn).click(function (e) {
                e.preventDefault();
                var items = $("[name='side-order-input']:checked");
                var checkedItems = [];
                if (items.length > 0) {
                    $.each(items, function (k, v) {
                        checkedItems.push($(v).val());
                    });
                    billItems[oid].sideOrders = checkedItems.join(",");

                    row = $("#purchase_table tr")[oid + 1];
                    var existingSideOrderText = $(".side-order-txt");
                    if (typeof existingSideOrderText !== "undefined") $(existingSideOrderText).remove();
                    var sideOrderTxt = $("<b class='side-order-txt'>(With : " + billItems[oid].sideOrders + ")</b>");
                    $(row).find("td:first-child").append(sideOrderTxt);
                } else {
                    $(".side-order-txt").remove();
                }
                unPauseBiller();
                $(sideOrdersWrapper).remove();
            });
            $(sideOrdersList).append(saveBtn);
            $(sideOrdersList).appendTo(sideOrdersWrapper);
            $(".pos_biller").prepend(sideOrdersWrapper);
        });



        $(prod_list).on('click', '.prod', function (e) {

            e.preventDefault();

            //{product_name,product_price,product_id,qty,}
            var newQty = 1;

            var product = {
                price: parseFloat($(this).attr("data-price")),
                name: $(this).attr("data-name"),
                id: $(this).attr("data-id"),
                stock_id: $(this).attr("data-stock_id"),
                qty: newQty,
                idstore: $(this).attr("data-store_id"),
                sideOrders: "",
                total: 0
            };

            addProductToBill(product, true);
        });

        $(table).on("change", ".price-field", function () {

            //var qty = parseInt($(this).val());
            var id = $(this).parent().parent().attr("data-oid");
            var price = parseInt($(this).val());
            var rowTotalField = $(".row_total" + id);


            if (!isNaN(id) && !isNaN(price)) {

                var prevTotal = billItems[id].total;
                qty = billItems[id].qty;
                billItems[id].price = price;

                billItems[id].total = billItems[id].price * qty;

                billTotal -= prevTotal;
                billTotal += billItems[id].total;


                //When updating suspended bill
                if (updateMode) {
                    try {
                        recentlyAddedId = DBUpdate.newItems.findIndex(function (el, ind, array) {
                            if (typeof el.id == "undefined") {
                                return false;
                            }
                            if (el.id == billItems[id].id) {
                                return true;
                            } else {
                                return false;
                            }
                        });


                        //search Existing itme
                        existingid = DBUpdate.toUpdate.findIndex(function (el, ind, array) {

                            if (el.id == billItems[id].id) {
                                //return true;
                            } else {
                                //return false;
                            }
                        });

                        //alert(existingid);

                        //Update newly inserted elements
                        if (recentlyAddedId > -1) {
                            DBUpdate.newItems[recentlyAddedId] = billItems[id];

                            //return;
                        }

                        if (existingid > -1) {
                            DBUpdate.toUpdate[existingid] = billItems[id];
                            return;
                        }

                        DBUpdate.toUpdate.push(billItems[id]);
                    } catch (ex) {
                        alert(ex);
                    }

                }

                $(rowTotalField).val(billItems[id].total);
                taxTotal = (billTotal * 18) / 100;

                $(taxField).val(taxTotal);

                $(billTotalField).val(billTotal);

            } else {
                $(this).val(1);
                //alert("Only numbers are allowed");
            }
        });

        $(table).on("change", ".qty_box", function () {

            var qty = parseFloat($(this).val());
            var id = parseInt($(this).parent().parent().parent().attr("data-oid"));

            var rowTotalField = $(".row_total" + id);


            if (!isNaN(id) && !isNaN(qty)) {

                if (qty == 0) return false;

                var prevTotal = billItems[id].total;

                billItems[id].total = billItems[id].price * qty;
                billTotal -= prevTotal;
                billTotal += billItems[id].total;
                billItems[id].qty = qty;

                //When updating suspended bill
                if (updateMode) {
                    try {
                        recentlyAddedId = DBUpdate.newItems.findIndex(function (el, ind, array) {
                            if (typeof el.id == "undefined") {
                                return false;
                            }
                            if (el.id == billItems[id].id) {
                                return true;
                            } else {
                                return false;
                            }
                        });


                        //search Existing itme
                        existingid = DBUpdate.toUpdate.findIndex(function (el, ind, array) {

                            if (el.id == billItems[id].id) {
                                //return true;
                            } else {
                                //return false;
                            }
                        });

                        //alert(existingid);

                        //Update newly inserted elements
                        if (recentlyAddedId > -1) {
                            DBUpdate.newItems[recentlyAddedId] = billItems[id];

                            //return;
                        }

                        if (existingid > -1) {
                            DBUpdate.toUpdate[existingid] = billItems[id];
                            return;
                        }

                        DBUpdate.toUpdate.push(billItems[id]);
                    } catch (ex) {
                        alert(ex);
                    }

                }

                $(rowTotalField).val(billItems[id].total);
                taxTotal = (billTotal * 18) / 100;

                $(taxField).val(taxTotal);

                $(billTotalField).val(billTotal);

            } else {
                $(this).val(1);
                //alert("Only numbers are allowed");
            }
        });

        $(".save_print_order").click(function (e) {
            e.preventDefault();

            if ($(this).attr("disabled") == "disabled") {
                return;
            }

            var btn = $(this);
            btnContents = $(btn).html();

            var tableID = parseInt($("#table").val());
            var waiterField = $('[name="waiterid"]:checked').val();
            var waiter_id = typeof waiterField == "undefined" ? 0 : waiterField;
            var store_id = parseInt($("#store").val());


            if ($("#table").val() == 0) {
                alert("Please choose table");
                $(btn).html(btnContents).removeAttr("disabled");
                return;
            }


            if ($("#store").val() < 1) {
                alert("Please choose order destination store !");
                $(btn).html(btnContents).removeAttr("disabled");
                return;
            }

            if (billItems.length == 0) {
                alert("There are no items on the order");
                $(btn).html(btnContents).removeAttr("disabled");

                return;
            }

            $(btn).attr("disabled", "disabled");
            $(btn).html("Saving ...");

            var billData = { "data": JSON.stringify(billItems), "stock": store_id, "table_id": tableID, "billTotal": billTotal, "waiter_id": waiter_id, "waiter_pin": $("#waiter-pin-input").val(), "taxTotal": taxTotal };

            $.ajax({
                url: options.saveOrderUrl,
                type: "post",
                data: billData,
                success: function (data) {
                    try {
                        var res = JSON.parse(data);
                        if (res.errors.length == 0) {
                            ualert.success("Your order has been saved");

                            var orderDate = new Date(Date.parse(res.date));

                            var savedBillData = {
                                idorders: res.idorders,
                                bill_total: billTotal,
                                tax_total: taxTotal,
                                waiter_id: waiter_id,
                                date: res.date,
                                items: billItems
                            };

                            $(btn).html(btnContents).removeAttr("disabled");

                            printOrder(res.idorders, { id: waiter_id, pin: $("#waiter-pin-input").val() });
                            ResetPOS();
                            $(".waiter-login-wrapper").toggleClass("hidden");
                            resetOrderForm();
                        } else {
                            alert("Error saving bill :" + (res.errors !== "undefined" && res.errors.length > 0 ? res.errors[0] : ""));
                            $(btn).html(btnContents).removeAttr("disabled");
                        }
                    } catch (ex) {
                        $(btn).html(btnContents).removeAttr("disabled");
                        alert("Server response error : " + ex.message);
                    }

                    
                },
                error: function (xhr, stat, error) {
                    alert("Error sending data: " + error);
                    $(btn).html(btnContents).removeAttr("disabled");
                }

            }).done(function () {
                //ResetOrderForm();
            });
        });

        //Item Delete 
        $(table).on("click", ".delete_btn", function (e) {

            e.preventDefault();
            var id = parseInt($(this).attr("data-id"));
            var prod_id = parseInt($(this).attr("data-prod_id"));

            if (isNaN(id) || isNaN(prod_id)) {
                alert("Unable to remove item , Please reload the page");
                return;
            }

            if (updateMode) {

                recentlyAddedId = DBUpdate.newItems.findIndex(function (el, ind, array) {
                    if (typeof el.id == "undefined") {
                        return false;
                    }
                    if (el.id == prod_id) {
                        return true;
                    } else {
                        return false;
                    }
                });

                recentlyUpdatedId = DBUpdate.toUpdate.findIndex(function (el, ind, array) {
                    if (typeof el.id == "undefined") {
                        return false;
                    }
                    if (el.id == prod_id) {
                        return true;
                    } else {
                        return false;
                    }
                });


                if (recentlyAddedId > -1) {

                    DBUpdate.newItems.splice(recentlyAddedId, 1);

                } else {

                    //Cancel Update
                    if (recentlyUpdatedId > -1) {
                        DBUpdate.toUpdate.splice(recentlyUpdatedId, 1);
                    }

                    DBUpdate.toDelete.push(prod_id);
                }
            }


            billTotal -= billItems[id].total;

            delete billItems[id];
            $(billTotalField).val(billTotal);

            taxTotal = (billTotal * 18) / 100;

            $(taxField).val(taxTotal);
            $("tr[data-oid='" + id + "']").remove();
        });

        $(".cancel_btn").click(function (e) {
            e.preventDefault();
            ResetPOS();
        });

        $(".pos-search-btn").click(function () { $(".pos-search-field").toggle().focus() });

        $(".pos-search-field").keyup(function (e) {

            var items = $("#suspended_bills_list").find("li");
            var searchVal = $(this).val();

            $.each(items, function (key, value) {
                id = $(value).children("b").html();

                if (typeof id != "undefined") {
                    id = id.replace("#", "");

                    if (id == searchVal) {
                        $(value).parent().parent().children("a").trigger("click");

                        $(value).children("b").css({
                            background: "rgb(105, 231, 17)",
                            "padding": "2px 5px"
                        })

                        $(".suspendedbills_btn").trigger("click");
                    }
                }
            })

        });

        $(".print_btn").click(function (e) {
            e.preventDefault();
            if ($(this).attr("disabled") == "disabled") {
                return false;
            }

            $(this).attr("disabled", "disabled");

            if ((DBUpdate.toDelete.length + DBUpdate.toUpdate.length + DBUpdate.newItems.length) > 0) {
                ualert.error("You must save change before printing");
                return;
            }

            var svBillID = $("#server_bill_id").val();
            //var spBillID = $("#local_bill_id").val();
            var ID = 0;

            if (svBillID > 0) {
                ID = parseInt(svBillID);
            }

            if (!isNaN(ID) && ID > 0) {
                printBill(ID);

            } else {
                ualert.error("There is no Bill to print , The bill must be suspended before printing");
                $(this).removeAttr("disabled");
            }

            $(this).removeAttr("disabled");
        })

        $(".product_lists").click(function () {

            if ($(".pos_mask").length > 0) {
                $(".pos_mask").remove();
                $(".pay_box").hide();
            }

        });


        //Search Products

        $("#product_search").keyup(function (e) {
            if ($(searchTextBox).val().length > 0) {
                searchProduct($(searchTextBox).val());
            }
        });

        function searchProduct(name) {
            $.ajax({
                url: options.searchUrl,
                type: "get",
                data: { q: name },
                success: function (data) {
                    $(resultsDiv).html("");
                    $(resultsList).html("");

                    try {
                        resData = JSON.parse(data);

                        $.each(resData, function (index, value) {
                            item = $("<li>").attr({
                                "data-price": value.price,
                                "data-id": value.id,
                                "data-name": value.product_name,
                                "data-stock_id": value.stock_id,
                                "data-store_id": value.idstore
                            }).addClass('search_prod').html(value.product_name + " <span>(" + value.price + ")</span> <input type='number' min='1' value='1' class='_prod-qty' />");

                            $(resultsList).append(item);
                        });


                        $(resultsDiv).html($(resultsList));
                        $(resultsDiv).prepend('<button class="search_close_btn"><i class="fa fa-times-circle"></i></button>');
                        if (resData.length == 0) {
                            $(resultsDiv).html("Nothing found");
                        }

                        $(searchTextBox).parent().append(resultsDiv);

                    } catch (e) {
                        ualert.error("Search : Data format error");
                    }
                },
                statusCode: {
                    401: function () {
                        ualert.error("Your session has expired please logout and login again !");
                        setTimeout(function () {
                            window.location.reload();
                        }, 3000)
                    }
                }
            })
        }


      

        $(".pos_biller").on("click", '.search_prod', function (e) {

            e.preventDefault();
            
            if (e.target.nodeName == "INPUT") {
                return false;
            }


            var _qty = $(this).find("._prod-qty").length > 0 ? $(this).find("._prod-qty").val() : 1;

            var product = {
                price: parseFloat($(this).attr("data-price")),
                name: $(this).attr("data-name"),
                id: $(this).attr("data-id"),
                stock_id: $(this).attr("data-stock_id"),
                idstore: $(this).attr("data-store_id"),
                qty: _qty,
                sideOrders : "",
                total: 0
            };

            addProductToBill(product, true);
            $(resultsDiv).html("").remove();
            $(searchTextBox).val("");

        });

        //close search results 

        $(".pos_biller").on("click", '.search_close_btn', function (e) {
            e.preventDefault();
            $(resultsDiv).remove();
        });

     
        $(".cancel_login_btn").click(function (e) {
            e.preventDefault();
            $("#waiter-pin-input").val("");
            $(".waiter-login-wrapper").toggleClass("hidden");
        });

        function resetOrderForm()
        {
            $("#waiter-pin-input").val("");
            $("[name=waiterid]:checked").removeAttr("checked").parent().removeClass("active");
            $("#table").val("").trigger("chosen:updated");
            $("#store").val("").trigger("chosen:updated");
        }

        function ResetPOS() {
            //Reset Objects
            billTotal = 0;
            billItems = [];
            products = {};
            taxTotal = 0;
            updateMode = false;

            DBUpdate = {
                toDelete: [],
                toUpdate: [],
                newItems: []
            }

            $("#server_bill_id").val("-1");
            $("#local_bill_id").val("-1");

            var titleRow = $("#purchase_table tr:first-child").clone();
            $(table).html($(titleRow));
            $(taxField).val("0");
            $(customerField).val("Walkin");
            $(billTotalField).val("0");
            $(updateBtn).parent().remove();
            $(buttonsContainer).find(".suspend_btn").parent().show();

            $("#waiter").val("0").trigger("chosen:updated");
            $(".pos_mask").remove();
            $(".pay_box").hide();
        }


        // update mode is used to add products to a saved bill , which will later be updated
        function addProductToBill(product, enableUpdateMode) {
            
            enableUpdateMode = typeof enableUpdateMode === 'undefined' ? false : true;
            //check if product exists and add quantity only
          

            var objID = billItems.length;

            var prod_qty = NumericBox(product.qty);

            var row = $("<tr>").attr("data-oid", objID).attr("data-prodid", product.id);;

            product.total = product.price * product.qty;
           
            /** Columns **/
            var nameCol = $("<td>").html(product.name);
            var priceInput = $("<input type='text' class='price-field' />");
            $(priceInput).val(product.price);
            var priceCol = $("<td>").html($(priceInput));
            var qtyCol = $("<td>").html($(prod_qty));
            var totalCol = $("<td>").html($("<input type='text' readonly='readonly' class='row_total row_total" + objID + "'>").val(product.total));
            var deleteBtn = $("<button data-prod_id='" + product.id + "' data-id='" + objID + "' class='delete_btn'>").html("<i class='fa fa-trash'></i>");

            var sideOrderBtn = $("<button data-prod_id='" + product.id + "' data-id='" + objID + "' class='sd-order_btn'>").html("<i class='fa fa-shopping-cart'></i>");

            billTotal += product.total;
            taxTotal = (billTotal * options.taxPercent) / 100;

            billItems.push(product);

            $(row).append(nameCol);
            $(row).append(priceCol);
            $(row).append(qtyCol);
            $(row).append(totalCol);
            $(row).append($("<td>").html(deleteBtn).prepend(sideOrderBtn));

            $(table).append($(row));

            $(billTotalField).val(billTotal);
            $(taxField).val(taxTotal);

            if (updateMode && enableUpdateMode) {
                DBUpdate.newItems.push(product);
            }
        }

        return this;
    }
}(jQuery));