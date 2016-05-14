

(function($) { 
	if(typeof JSObj !== "undefined")
	{
	    window.confirm = function (text) {
	        return JSObj.confirm(text);
	    };
		//working in ORGBox

		JSObj.ToolStripBG(44, 62, 83);
		JSObj.ToolStripColor(215, 215, 215);
	}
	
//Loading Products
	$.fn.loadProducts = function(options) { 
		$.ajaxSetup({
   			 headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() }
    	});

		var elm = this;
		var progressbar = $(".progress");
		
		$(progressbar).show();
		$(".recent_prods").html($(progressbar).html());
		$(progressbar).children(".progress-bar").css("transition","2.2s all").width("40%");

		$.extend({ 
			url:"/POS/Products/json",
			category_id:0,
			store_id: 0,
            favorite:true
	    }, options );

	    $.ajax({
				url:options.url+"?store="+options.store_id+"&category="+options.category_id+((options.favorite) ? "&favorite=1" : ""),
				type:"get",
				success:function(data){

					try {
					    products = JSON.parse(data);
				    }catch(ex){
				    	alert(ex.message);
				    }

					var prodItem;
					var list = $("<ul class=\"shown\">");

	                $.each(products,function(index,value){
	                	prodItem = $("<li>").addClass("prod");
		                $(prodItem).attr("data-price",value.price);
						$(prodItem).attr("data-name",value.product_name);
						$(prodItem).attr("data-id",value.id);
						$(prodItem).attr("data-stock_id",value.stock_id);
						$(prodItem).html('<i class="fa fa-cutlery"></i>'+value.product_name);
						$(list).append($(prodItem));
	                });

	                $(progressbar).children(".progress-bar").css("transition",".6s all").width("100%");
	                setTimeout(function(){
	                	$(".recent_prods").html("").append(list);
	                	 $(progressbar).hide();
	                },600)
	                
				},
				complete:function(){
					//$(progressbar).hide();
				}

			});
	}

    $.fn.billOperations = function(options){

		var billTotal = 0;
		var billItems = [];
		var products = {};
		var taxTotal = 0;
		var DBUpdate = {
			toDelete : [],
			toUpdate : [],
			newItems : []
		};

		var updateMode = false;
  
		var suspendedBills = []; // { idbills:, bill_total:, tax_total:, customer:, waiter_name:, waiter_id: 1, date:}
		var billTotalField = $("#totalPayable");
		var taxField = $("#tax");
		var table = $("#purchase_table");
		var suspList = $("#suspended_bills_list");
		var customerField = $("#customer");
	    var waiterField = $("#waiter");
	    var billdelBtn = $('<button class="bill_delete_btn text-danger">').html('<i class="fa fa-trash-o"></i>');
		var billOpenBtn = $('<button class="bill_open_btn text-success">').html('<i class="fa fa-eye"></i>');
		var billShareBtn = $('<button class="bill_share_btn text-primary">').html('<i class="fa fa-random"></i>');
		var elm = this;
        var prod_list = $(".product_lists");
		var updateBtn = $("<a href='#' class='update_btn'>").html("Update");
		var buttonsContainer = $(".pos_biller .actions > ul");
		var searchTextBox = $("#product_search");
		var resultsDiv = $("<div class='search_results'>").prepend("<i class='search_close_btn fa fa-times'>");
		var resultsList = $("<ul>");

		//Shortcut Keys

		$(document).keypress(function(e){
			if(e.key.toLowerCase()=="f2")
			{
				//Save Bill
				//$(".suspend_btn").trigger("click");
			}

			if(e.key.toLowerCase()=="f3")
			{
				//print
				$(".print_btn").trigger("click");
			}
		})
		//@end shortcuts

        $.ajaxSetup({
   			 headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() }
    	});

  		//Load suspended bills from server
        loadSuspendedBills();

		$(prod_list).on('click','.prod',function(e){

			e.preventDefault();
			
			//{product_name,product_price,product_id,qty,}

			var product = {
				price : parseFloat($(this).attr("data-price")) ,
				name: $(this).attr("data-name"),
				id : $(this).attr("data-id"),
				stock_id:$(this).attr("data-stock_id"),
				qty:1,
				total: 0
			};

			addProductToBill(product,true);
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
		        alert("Only numbers are allowed");
		    }
		});

		$(table).on("change",".qty_box",function(){

			var qty = parseFloat($(this).val());
			var id = parseInt($(this).parent().parent().parent().attr("data-oid"));

			var rowTotalField = $(".row_total"+id);
    		

				if(!isNaN(id) && !isNaN(qty)){

					if(qty==0) return false;

					var prevTotal = billItems[id].total;

					billItems[id].total =  billItems[id].price * qty;
					billTotal -= prevTotal;
					billTotal += billItems[id].total;
					billItems[id].qty = qty;

					//When updating suspended bill
					if(updateMode) {
						try {
						recentlyAddedId =  DBUpdate.newItems.findIndex(function(el,ind,array){
							if(typeof el.id =="undefined"){
								return false;
							}
							if(el.id == billItems[id].id){
								return true;
							}else {
								return false;
							}
						});
						

						//search Existing itme
						existingid = DBUpdate.toUpdate.findIndex(function(el,ind,array){
							
							if(el.id == billItems[id].id){
								//return true;
							}else {
								//return false;
							}
						});

						//alert(existingid);

						//Update newly inserted elements
						if(recentlyAddedId>-1){
							DBUpdate.newItems[recentlyAddedId] = billItems[id];

							//return;
						}

						if(existingid>-1){
							DBUpdate.toUpdate[existingid] = billItems[id];
							return;
						}

						DBUpdate.toUpdate.push(billItems[id]);
						}catch(ex){
							alert(ex);
						}
												
					}

					$(rowTotalField).val(billItems[id].total);
					taxTotal = (billTotal*18)/100;

					$(taxField).val(taxTotal);

					$(billTotalField).val(billTotal);

				}else {
					$(this).val(1);
					alert("Only numbers are allowed");
				}
		});

		//Item Delete 
		$(table).on("click",".delete_btn",function(e){

			e.preventDefault();
			var id = parseInt($(this).attr("data-id"));
			var prod_id = parseInt($(this).attr("data-prod_id"));

			if(isNaN(id) || isNaN(prod_id)){
				alert("Unable to remove item , Please reload the page");
				return;
			}

			if(updateMode) {

				recentlyAddedId =  DBUpdate.newItems.findIndex(function(el,ind,array){
					if(typeof el.id =="undefined"){
						return false;
					}
					if(el.id == prod_id){
						return true;
					}else {
						return false;
					}
				});

				recentlyUpdatedId =  DBUpdate.toUpdate.findIndex(function(el,ind,array){
					if(typeof el.id =="undefined"){
						return false;
					}
					if(el.id == prod_id){
						return true;
					}else {
						return false;
					}
				});


				if(recentlyAddedId > -1){
					
					DBUpdate.newItems.splice(recentlyAddedId,1);
				
				}else {

					//Cancel Update
					if(recentlyUpdatedId > -1){
						DBUpdate.toUpdate.splice(recentlyUpdatedId,1);
					}

					DBUpdate.toDelete.push(prod_id);
				}
			}
			
			
			billTotal -= billItems[id].total;

			delete billItems[id];
			$(billTotalField).val(billTotal);

			taxTotal = (billTotal*18)/100;

			$(taxField).val(taxTotal);
			$("tr[data-oid='"+id+"']").remove();
		});

		//Suspend btn 
		$(".suspend_btn").click(function(e){
			e.preventDefault();

			if($(this).attr("disabled")=="disabled"){
				return;
			}

			var btn = $(this);
			btnContents = $(btn).html();

			if($("#waiter").val() ==0){
				alert("Please choose waiter");
				$(btn).html(btnContents).removeAttr("disabled");

				return;
			}

			if(billItems.length ==0){
				alert("There are no items on the bill");
				$(btn).html(btnContents).removeAttr("disabled");

				return;
			}

			$(btn).attr("disabled","disabled");
			$(btn).html("Saving ...");

			var waiterID = parseInt($("#waiter").val());
			var waiterName = $("#waiter option:selected").html();

			
			  var customer = $("#customer").val();
			  var billData = {"data":JSON.stringify(billItems),"waiter_id":waiterID,"waiter_name":waiterName,"billTotal":billTotal,"taxTotal":taxTotal,"customer":customer};

			$.ajax({
				url:options.suspendUrl,
				type:"post",
				data: billData,
				success:function(data){
					try {
						var res = JSON.parse(data);
						if(res.errors.length ==0){
							ualert.success("Bill Saved");

							var billDate = new Date(Date.parse(res.date));

							var savedBillData = { 
								idbills:res.idbills, 
								bill_total: billTotal,
								tax_total:taxTotal, 
								customer:customer,
								waiter_name:waiterName, 
								waiter_id: waiterID, 
								date: res.date,
								items :billItems
							};

							existing_bill_id = parseInt($("#local_bill_id").val());//When modify an existing bill

							if (existing_bill_id == -1) {
							    suspendedBills.push(savedBillData);
                               
							} else {
							  
							    suspendedBills[existing_bill_id] = savedBillData;
							    //reset
							    $("#local_bill_id").val("-1");
                                //get out
								$(btn).html(btnContents).removeAttr("disabled");

							    return; 
							}
							
							//Add bill to suspendedlist
							localID = suspendedBills.length-1;
							addBillToList(savedBillData,localID);

							$("#local_bill_id").val(localID);
							$("#server_bill_id").val(savedBillData.idbills);
							$("#server_bill_id").attr("value", savedBillData.idbills);
							initiateUpdateMode();
							
							$(btn).html(btnContents).removeAttr("disabled");
							$(customerField).attr("value",$(customerField).val());

						}else {
							alert("Error saving bill");
							$(btn).html(btnContents).removeAttr("disabled");
						}
					}catch(ex){
							$(btn).html(btnContents).removeAttr("disabled");

						alert("Server response error : "+ex.message);
					}
				},
				error:function(xhr,stat,error){
					alert("Error sending data: "+ error);
					$(btn).html(btnContents).removeAttr("disabled");

				}

			})
		})


		$(".cancel_btn").click(function(e){
			e.preventDefault();
			ResetPOS();
		});

		$(".pos-search-btn").click(function(){ $(".pos-search-field").toggle().focus() });

		$(".pos-search-field").keyup(function(e){

			var items = $("#suspended_bills_list").find("li");
			var searchVal = $(this).val();

			$.each(items,function(key,value){
				id = $(value).children("b").html();

				if(typeof id != "undefined")
				{
					id  = id.replace("#","");

					if(id==searchVal)
					{ 
						$(value).parent().parent().children("a").trigger("click");

						$(value).children("b").css({
							background:"rgb(105, 231, 17)",
							"padding":"2px 5px" 
						}) 

						$(".suspendedbills_btn").trigger("click");
					}
				}
			})
  
		});

		$(".print_btn").click(function(e){
			e.preventDefault();
			if($(this).attr("disabled")=="disabled")
			{
				return false;
			}

			$(this).attr("disabled","disabled");

			if((DBUpdate.toDelete.length + DBUpdate.toUpdate.length + DBUpdate.newItems.length) > 0)
			{
				ualert.error("You must save change before printing");
				return;
			}

			var svBillID = $("#server_bill_id").val();
			//var spBillID = $("#local_bill_id").val();
			var ID  = 0;

			if(svBillID > 0)
			{
				ID = parseInt(svBillID);		
			}

			if(!isNaN(ID) && ID > 0)
			{
				printBill(ID);

			}else 
			{
			    ualert.error("There is no Bill to print , The bill must be suspended before printing");
			    $(this).removeAttr("disabled");
			}

			$(this).removeAttr("disabled");
		})

		$(".pay_box").on( "click", function(e){
			e.stopPropagation();
		});

		$(".pay_box").on("click",".pay_box_close_btn",function(e){
			e.preventDefault();
			$(".pos_mask").remove();
			$(".pay_box").hide();
		});

		$(".product_lists").click(function(){
         
			if($(".pos_mask").length > 0){
				$(".pos_mask").remove();
				$(".pay_box").hide();
			}

		});

		$(".pay_btn").click(function(e){
			e.preventDefault();
			
			//check if customer changed

			if(parseInt($("#server_bill_id").val())>0 && $(customerField).val()!=$(customerField).attr("value"))
			{
				ualert.error("Please Update changes made to the bill (Customer Name)");
				return false;
			}

			if((DBUpdate.toUpdate.length + DBUpdate.newItems.length + DBUpdate.toDelete.length) > 0){
				alert("You have not updated the changes made , Please update");
				return;
			}

			if($(".pos_mask").length == 0){
			   pause = pauseBiller();
		    }else {
		    	$(".pos_mask").remove();
		    }

			$(this).css({
				"z-index":"100",
				"position":"relative"
			});

			var splitPayBtnWrapper = $("<span class='input-group-addon'>");
			var splitPayBtn = $("<button id='split-pay-btn'>");
			$(splitPayBtn).html("<i class='fa fa-money'></i>");
			$(splitPayBtnWrapper).html($(splitPayBtn));

			var splitPayWrapper = $('<div class="input-group">');
			$(splitPayWrapper).html($(splitPayBtnWrapper));

			var paymentInput = $("<input type='text' data-split-pay='0' class='paid_amount'>").val(0);
			$(splitPayWrapper).append(paymentInput);

			var amountDueInput  = $("<input type='text' readonly='readonly' class='amount_due'>").val(billTotal);
			var changeInput = $("<input type='text' readonly='readonly' class='change_amount'>").val(0);
			var payBtn = $("<button data-print='0' class='pay_print pay'>Pay</button>");
			var payPrintBtn = $("<button data-print='1' class='pay_print'>Pay & Print</button>");

			var advancedBox = $("<div class='advancedBox'>");
			$(advancedBox).append("<label>Pay. Method / Pay. Mode</label>").append("<select id='pay_method' name='method'><option>Cash</option><option>Bank Card</option><option>Check</option></select>").append("<select id='pay_mode' name='pay_mode'><option>Debit</option><option>Credit</option><option value='0'>Off-Tariff</option>").append("<label>Comment</label>").append("<input id='pay_comment' name='comment' type='text'>");
			
			var innerPayBox = $("<div>").append("<a class='bill_transfer_btn' href='#'><i class='fa fa-bed'></i></a>").append("<a class='more_options_btn' href='#'><i style='font-size:22px' class='fa fa-cc-visa'></i></a>").append(advancedBox)
			.append("<br/><label>Amount Paid</label>")
			.append(splitPayWrapper).append("<label>Amount Due</label>").append(amountDueInput).append("<label>Change</label>")
			.append(changeInput).append(payPrintBtn).append(payBtn);

			$(".pay_box").html("");
			var payBox = $(".pay_box").append("<p class='text-success text-center'><i class='fa fa-file-word-o'></i> Bill Payment <span><i class='fa fa-times pay_box_close_btn'></i></span></p>").append(innerPayBox);
			
			$(payBox).toggle();
		
		});
		

		$(".grid").on("keyup",'.paid_amount',function(e){

	       	var amount = parseInt($(this).val());
			var amount_due = parseInt(billTotal);

			if(!isNaN(amount) && !isNaN(amount_due)){
				change_returned = amount-amount_due;
				change_returned = change_returned < 0 ? 0 : change_returned;
				$(".change_amount").val(change_returned);
			}else {
				$(".change_amount").val(0);

			}

		});

		$(".pay_box").on("keyup",".cash-amount",function(e){
			cash_amount  = parseFloat($(this).val());
			card_amount = parseFloat($(".bankcard-amount").val());

			if(!isNaN(cash_amount) && !isNaN(card_amount))
			{
				$(".total-split-amount").val(cash_amount+card_amount);
			}else {
				ualert.error("Only numbers are allowed");
			}

		});



		$(".pay_box").on("keyup",".bankcard-amount",function(e){
			card_amount  = parseFloat($(this).val());
			cash_amount = parseFloat($(".cash-amount").val());

			if(!isNaN(cash_amount) && !isNaN(card_amount))
			{
				$(".total-split-amount").val(cash_amount+card_amount);
			}else {
				ualert.error("Only numbers are allowed");
			}

		});


		$(".pay_box").on("click",'.more_options_btn',function(e){
			e.preventDefault();
			$(".advancedBox").toggle();
		});

		$(".pay_box").on("click",'.bill_transfer_btn',function(e){
			e.preventDefault();
			box = $("<div class='room_transfer_box'>").append("<label>Room Number</label><input type='text' placeholder='Room' id='transfer_bill_toroom' /><button id='bill_transfer_btn'>Assign</button>");
			if($(".room_transfer_box").length ==0 ){
				$(".pay_box").append(box);
			}else {
				$(".room_transfer_box").remove();
			}

		});

		$(".pay_box").on("change","#pay_mode",function(){
			if($(this).val().toLowerCase()!="debit")
			{
			    $("#pay_method").selectedIndex = 0;
			    $("#pay_method").attr("disabled", "disabled");
				$(".paid_amount").val("0").trigger("keyup");
			}else {
				$("#pay_method").removeAttr("disabled");
			}
			
		});

		$(".pay_box").on("click",'#bill_transfer_btn',function(e){
			e.preventDefault();
			var billID = parseInt($("#server_bill_id").val());
			var room = $("#transfer_bill_toroom").val();

			if(isNaN(billID) || billID < 1){
				ualert.error("Invalid Bill");
				return;
			}

			if((DBUpdate.toUpdate.length + DBUpdate.newItems.length + DBUpdate.toDelete.length) > 0){
				alert("You have not updated the changes made , Please update");
				return;
			}

			if($("#transfer_bill_toroom").val().length < 1){
				ualert.error("You must enter a room to which you want to assign the bill ");
				return;
			}
			var info = {BillID : billID,Room:room,due:billTotal};
			$.ajax({
				url : options.assignBillUrl,
				type: "POST",
				data : info,
				success : function(data){
					if(data==="1"){
						ualert.success("Bill Assigned to Room "+info.Room);
						removeBillFromUI(info.BillID);
						ResetPOS();
					}else {
						ualert.error("Error Assigning the bill to room "+info.Room+", Please check if it's the right room");
					}
				}
			});;
		});

		//Split Payment
		$(".pay_box").on("click","#split-pay-btn",function(e){
			e.preventDefault();
			$(".paid_amount").val("0").trigger("keyup");
			cashBox = $("<input type='text' class='cash-amount'>");
			bankCardBox = $("<input type='text' class='bankcard-amount'>");
			commentBox = $("<input type='text' class='text-left comment-box'>");

			$(bankCardBox).val("0");
			$(cashBox).val("0");

			var OKBtn = $("<button data-splitpay='1' class='pay_print splitpay-ok-btn btn btn-success'>");
			var CancelBtn = $("<button class='splitpay-cancel-btn btn btn-danger'>");
			var TotalInput = $("<input readonly='readonly' type='text' class='total-split-amount'>");
			var splitPayBox = $("<div class='split-pay-box'>");
			$(OKBtn).html("Pay");
			$(CancelBtn).html("Cancel");

			$(TotalInput).val("0");

			$(splitPayBox).html("");

			$(splitPayBox).append("<label>Cash Amount</label>").append(cashBox).append("<label>Bank Card Amount</label>").append(bankCardBox)
			.append("<label>Comment</label>").append(commentBox)
			.append("<label class='total-label'>Total</label>").append(TotalInput).append(OKBtn).append(CancelBtn);

			$(".pay_box").prepend(splitPayBox);

		});

		$(".pay_box").on("click",'.splitpay-cancel-btn',function(e){
			e.preventDefault();
			$(".split-pay-box").remove();
		})
		///Paying the bill  - Pay Bill
        $(".pay_box").on("click",".pay_print",function(e){
            
            //region VALIDATION
        	var splitPay = $(".split-pay-box").length > 0 ? true : false;

        	if(billItems.length == 0)
        	{
        		ualert.error("There are no items on the bill");
        		return false;
        	}
        	if($(this).attr("disabled")=="disabled")
        	{
        		return false;
        	}

        	if($('#pay_mode').val()=='Credit' && $("#customer").val().toLowerCase()=="walkin" && parseFloat($(".paid_amount").val()) == 0 )
			{
				ualert.error("Please Enter Customer Name");
				return false;
			}

			if($('#pay_mode').val()=='Off-Tariff' && $("#customer").val().toLowerCase()=="walkin")
			{
				ualert.error("Please Enter Free consumption customer name");
				return false;
			}

			if($('#pay_mode').val()=='Off-Tariff' && parseFloat($(".paid_amount").val()) > 0 )
			{
				ualert.error("Amount paid must be equal to 0 for free consumption customers");
				return false;
			}

            //Region End VALIDATION

        	$(this).attr("disabled","disabled");
        	btn = $(this);

        	if((DBUpdate.toUpdate.length+DBUpdate.toDelete.length+DBUpdate.newItems.length) > 0){
        		ualert.error("Bill changes have not been saved , Please save changes to continue");
        		return;
        	}

        	if($(this).attr("data-print")=="1"){
        		printTheBill = true;
        	}else {
        		printTheBill = false;
        	}

        	var amount = parseInt($(".paid_amount").val());
			var amount_due = parseInt(billTotal);
			var method =  $("#pay_method").val();
			var comment = $("#pay_comment").val();
			var mode = $("#pay_mode").val();
			
			if((amount-amount_due)<0 && mode.toLowerCase() =="debit" && !splitPay)
			{
			    ualert.error("Invalid amount paid , amount paid must be greater or equal to the total bill amount");
				return;
			}


			if((amount-amount_due)>=0 && mode.toLowerCase() =="credit")
			{
			    ualert.error("You cannot pay the total amount of the bill by credit");
				return;
			}

			var billID = parseInt($("#server_bill_id").val());
		
			//New Bill : if bill is not saved yet save it !
			if(isNaN(billID) || billID < 1 ){
			    alert("DSDs");
			    
				var waiterID = parseInt($("#waiter").val());
			    var waiterName = $("#waiter option:selected").html();


				var paidAmount = parseInt($(".paid_amount").val());
				var change_amount = paidAmount - billTotal;
				var billData = {"data":JSON.stringify(billItems),"waiter_id":waiterID,
					"waiter_name":waiterName,"billTotal":billTotal,"taxTotal":taxTotal,
					"customer":$(customer).val(),"pay_amount":paidAmount,"change_returned":change_amount,
					"comment":comment,"method":method,"mode":mode
				};


				$.ajax({
					url:options.suspendUrl,
					type:"post",
					data: billData,
					success : function(data){
						jData = JSON.parse(data);
						if(jData.errors.length ==0){
							ualert.success(jData.message);

							if(printTheBill){
								window.printBill(jData.idbills);
							}

							window.countSales();
							ResetPOS();
						}else {
							ualert.error(jData.errors[0]);
						}
					},
					error:function(){
						alert("Server Error");
					}

				}).done(function(){
					$(btn).removeAttr("disabled");
				})

				return;
			}


			var Bdata  = {
				billID :  billID,
				amountPaid : splitPay ?  parseFloat($(".total-split-amount").val())  : amount,
				amountDue :  billTotal,
				comment : splitPay ? $(".comment-box").val()  : comment,
				method: method,
				splitPayments: splitPay ? {"bankcard": parseFloat($(".bankcard-amount").val()),"cash":parseFloat($(".cash-amount").val()) } : {},
				mode : $("#pay_mode").val()
			};

			$.ajax({
			    url: options.paySuspendeBillUrl,
				type:"post",
				data:Bdata,
				success:function(data){
					jData = JSON.parse(data);

					if(typeof jData.errors != "undefined" && jData.errors.length > 0){
						ualert.error(jData.errors[0]);
						return;
					}
					
					ualert.success(jData.msg);

					if(printTheBill){
						window.printBill(billID);	
					}

					window.countSales();
					
					removeBillFromUI(billID);

					ResetPOS();
				},
				error:function(e)
				{
					ualert.error("An error Occurred while saving the bill , Please try again");
				}
			}).complete(function(){
				$(btn).removeAttr("disabled");
			});


        })

		//share bill
		$(suspList).on('click','.bill_share_btn',function(e){
			e.preventDefault();
			if(!confirm("Are you sure you want to share this bill ?")){
				return false;
			}

			if($(this).attr("disabled")=="disabled")
			{
				return false;
			}
			var shareAttr  = $(this).attr("data-shared");

			var local_id = parseInt($(this).attr("data-sus_id"));
			var db_id = parseInt($(this).attr("data-bill_id"));
			var id  = db_id;
			var isShared=(typeof shareAttr !== typeof undefined && shareAttr !== false) ? $(this).attr("data-shared") : 0;

			$(this).attr("disabled","disabled");
			btn = $(this);

			$.get(options.shareBillUrl+"?id="+id+"&shared="+isShared,function(data){
				if(data==0)
				{
					ualert.error("Unable to share the selected bill , Please try again");
					$(btn).removeAttr("disabled");
				}else {
					if(isShared==0)
					{
						$(btn).parent().prepend("<i class='fa fa-globe'></i>");
						ualert.success("Bill "+id+" has been marked as shared, it will be available in all cashiers' accounts");
					}else {
						ualert.success("Bill "+id+" has been marked as private, it will be available in your account only");
						$(btn).parent().find('.fa-globe').remove()
					}
					
				}
			})
		});

		//end share bill

		//Open Bill
		$(suspList).on('click','.bill_open_btn',function(e){

			e.preventDefault();
			$(this).attr("disabled","disabled");
			
			
			var local_id = parseInt($(this).attr("data-sus_id"));
			var db_id = parseInt($(this).attr("data-bill_id"));

			ResetPOS();
			//local Bill
			if ( local_id > -1) {
				updateMode = true;
				//Add products from JS Object to the bill table
				$.each(suspendedBills[local_id].items,function(index,val){
					addProductToBill(val);
				});

				$(waiterField).val(suspendedBills[local_id].waiter_id).trigger("chosen:updated");
				$(customerField).val(suspendedBills[local_id].customer);
				$(customerField).attr("value", suspendedBills[local_id].customer);

				initiateUpdateMode();
	         	$("#server_bill_id").val(db_id);
	         	
				$(this).removeAttr("disabled");
	        }else {
	        	mask = pauseBiller();
	            //load bill from server
	            $.ajax({
	            	url:options.suspendedUrl+"?json&bill_id="+db_id,
	            	type:"get",
	            	success:function(data){
	        			initiateUpdateMode();

	            		Data = JSON.parse(data);
	            		thebill = Data[1];

	            		$(customerField).val(thebill.customer);
	            		$(customerField).attr("value", thebill.customer);
			            $(taxField).val(thebill.tax_total);
			            $(billTotalField).val(thebill.bill_total);
			            $(waiterField).val(thebill.waiter_id).trigger("chosen:updated");

			            

			            $.each(Data[0], function (key, value) {

			            	addProductToBill(value);

			            });

			            $(".local_bill_id").val();
			            $(".server_bill_id").val(db_id);
	            	},
	            	statusCode: {
					    401: function() {
					      ualert.error("Your session has expired please logout and login again !");
					      setTimeout(function(){
					      	window.location.reload();
					      },3000)
					    }
					}

	            }).done(function(){
	            	$(".bill_open_btn").removeAttr("disabled");
	            	unPauseBiller(mask);
	            });

	         $("#server_bill_id").val(db_id);
	        }


		});

		$(suspList).on('click', '.bill_delete_btn', function (e) {
		    if (!confirm("Are you sure you want to delete the bill ?")) {
		        return;
		    }
			var local_id = parseInt($(this).attr("data-sus_id"));
			var db_id = parseInt($(this).attr("data-bill_id"));
			var btn  = $(this);

			$.ajax({
				url:options.billDeleteUrl,
				type:"delete",
				data:{"id":db_id},
				success:function(data){
					if(parseInt(data)==1){
						$(btn).parent().remove();
						if(typeof suspendedBills[local_id] !="undefined"){
						 suspendedlist.splice(local_id,1);
						 
						}
						removeBillFromUI(db_id);
					}else {
						alert("Error deleting bill");
					}
				},
				error:function(){
					alert("Server Error");
				},
				statusCode: {
					    401: function() {
					      ualert.error("Your session has expired please logout and login again !");
					      setTimeout(function(){
					      	window.location.reload();
					      },3000)
					    }
					}
			})
		});

		$(".pos_biller .actions").on("click",'.update_btn',function(e){
			e.preventDefault();

			updates  = DBUpdate.newItems.length + DBUpdate.toUpdate.length + DBUpdate.toDelete;
			if (updates == 0 && $(customerField).val() == $(customerField).attr("value")) {
				ualert.warning("There Were no changes made");
				return;
			}

			if($(this).attr("disabled")=="disabled"){return;} //Cancel click when the button is disabled

			$(this).html("Updating ...");
			$(this).attr("disabled","disabled");

			id = parseInt($("#server_bill_id").val());
			
			var newCustomer = $(customerField).val() == $(customerField).attr("value") ? "" : $(customerField).val();

			var itemUpdates = JSON.stringify(DBUpdate);

			$.ajax({
				url:options.billUpdateUrl,
				type:"post",
				data: { bill_id: id, billTotal: billTotal, taxTotal: taxTotal, itemUpdates: itemUpdates, customer: newCustomer },
				success:function(data){
					
					if(parseInt(data)==1){
						ualert.success("Bill items updated successfuly !");
						DBUpdate = {
							toDelete : [],
							toUpdate : [],
							newItems : []
		    			}
		    			$(customerField).attr("value", $(customerField).val() );
					}else {
						alert("There was an error updating the bill !");
					}
				},
				statusCode: {
					    401: function() {
					      ualert.error("Your session has expired please logout and login again !");
					      setTimeout(function(){
					      	window.location.reload();
					      },3000)
					    }
					}
			}).done(function(){
				$(".update_btn").removeAttr("disabled");
				$('.update_btn').html("Update");
			})

		});

		//Search Products

		$("#product_search").keyup(function(e){
			if($(searchTextBox).val().length>0){
				searchProduct($(searchTextBox).val());
			}
		});

		function searchProduct(name)
		{
			$.ajax({
				url: options.searchUrl,
				type:"get",
				data : {q:name},
				success:function(data)
				{
					$(resultsDiv).html("");
					$(resultsList).html("");

					try {
					  	resData = JSON.parse(data);

					  	$.each(resData,function(index,value){
					  		item = $("<li>").attr({
					  			"data-price" : value.price,
					  			"data-id": value.id,
					  			"data-name" : value.product_name,
					  			"data-stock_id":value.stock_id
					  		}).addClass('search_prod').html(value.product_name + " <span>(" + value.price + ")</span> <input type='number' min='1' value='1' class='_prod-qty' />");

					  		$(resultsList).append(item);
					  	});

					  		
					  	$(resultsDiv).html($(resultsList));
					  	$(resultsDiv).prepend('<button class="search_close_btn"><i class="fa fa-times-circle"></i></button>');
					  	if(resData.length ==0){
					  		$(resultsDiv).html("Nothing found");
					  	}

					  	$(searchTextBox).parent().append(resultsDiv);

					}catch(e){
					  	ualert.error("Search : Data format error");
					}	
				},
				statusCode: {
					401: function() {
					    ualert.error("Your session has expired please logout and login again !");
					    setTimeout(function(){
					    window.location.reload();
					    },3000)
					}
				}
			})
		}

		$(".pos_biller").on("click",'.search_prod',function(e){

			e.preventDefault();
			console.log(e.target.nodeName);
			if(e.target.nodeName=="INPUT")
			{
				return false;
			}

				
			var _qty = $(this).find("._prod-qty").length > 0 ?  $(this).find("._prod-qty").val() : 1;

			var product = {
				price : parseFloat($(this).attr("data-price")) ,
				name: $(this).attr("data-name"),
				id : $(this).attr("data-id"),
				stock_id:$(this).attr("data-stock_id"),
				qty:_qty,
				total: 0
			};

			addProductToBill(product,true);
			$(resultsDiv).html("").remove();
			$(searchTextBox).val("");
				
		});

		//close search results 
			
		$(".pos_biller").on("click",'.search_close_btn',function(e){
			e.preventDefault();
			$(resultsDiv).remove();
		})


		//Custom product with custom price
		$("#custom_product_form").submit(function(e){
			e.preventDefault();
			var theForm = $(this);
			var prod_price = parseFloat($(this).children("input[name='product_price']").val());
			var prod_name = $(this).children("input[name='product_name']").val();

			if(isNaN(prod_price)){
				ualert.error("Price must be in number format");
				return;
			}

			if(prod_name.length < 2)
			{
				ualert.error("Invalid Product name");
				return;
			}

			$.ajax({
				url:$(this).attr("action"),
				"type":"post",
				"data":$(theForm).serialize(),
				"success":function(data){
					var id = parseInt(data);

					if(!isNaN(id)) // If it's a valid number
					{
						if(id>0)
						{
							var product = {
								price : prod_price,
								name: prod_name,
								id : id,
								qty:1,
								total: 0
							};

							addProductToBill(product);
							$(theForm).parent().toggle();
						}else {
							ualert.error("Unable to add product");
						}
					}
				},
				statusCode: {
					    401: function() {
					      ualert.error("Your session has expired please logout and login again !");
					      setTimeout(function(){
					      	window.location.reload();
					      },3000)
					    }
					}
			})
		});

		$(".customer-item-btn").click(function (e) {
		    e.preventDefault();
		    $("#customer").val($(this).attr("data-name"));
		})
	
		//Function Definitions


		function initiateUpdateMode()
		{
			//Go into update mode 
			updateMode = true;
			$(buttonsContainer).find(".suspend_btn").parent().hide();
			$(buttonsContainer).prepend($("<li>").html(updateBtn));
		}

		function ResetPOS()
		{
			//Reset Objects
			 billTotal = 0;
			 billItems = [];
			 products = {};
			 taxTotal = 0;
			 updateMode  = false;

			 DBUpdate = {
				toDelete : [],
				toUpdate : [],
				newItems : []
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

		function countSuspendedBills()
		{
			$(".suspendedbills_btn").children("span").html($("#suspended_bills_list").find("li.bill_item").length);
		}

		function removeBillFromUI(billID)
		{
			parent  = $(".bill_item_"+billID).parents("li");
			$(".bill_item_"+billID).remove();

			//Update counters
			if($(parent).children("ul:empty").length > 0){
				$(parent).remove();
			}

			$(parent).find("a > span").html($(parent).children("ul").children().length);

			countSuspendedBills();
		}

		function pauseBiller(){
			mask = $("<div class='pos_mask'>");
			$(mask).css({
				"position":"absolute",
				"left":"0",
				"right":"0",
				"top":"0",
				"bottom":"0",
				"width":"100%",
				"text-align":"center",
				"padding":"50% 0px",
				"background":"#fff",
				"background":"rgba(255, 255, 255, 0.8)",
				"font-size":"18px",
				"color":"#ccc",
				"z-index":"55"
			}).html("Loading ...")
			$(".pos_biller").prepend(mask);
			return mask;
		}

		function unPauseBiller(mask){
			$(mask).fadeOut(1200,function(){$(this).remove();});
		}

		function loadSuspendedBills()
		{

			$.ajax({
				url:options.suspendedUrl+"?json",
				type:"get",
				success:function(data){
					try {
					var bills  = JSON.parse(data);
					}catch(ex){
						alert("Data Error");
						return;
					}

					$.each(bills[1],function(index,res){
						//Add bill to suspendedlist
						addBillToList(res);
					})
					
				  },
				  statusCode: {
					    401: function() {
					      ualert.error("Your session has expired please logout and login again !");
					      setTimeout(function(){
					      	window.location.reload();
					      },3000)
					    }
					}
			  }).done(function(){
			  	countSuspendedBills();
			  })
	    }

	    function addBillToList (res,localbill_id)
	    {
	    	localbill_id= typeof localbill_id === 'undefined' ? '' : localbill_id;

	    	delBtn = $(billdelBtn).clone();
	    	openBtn = $(billOpenBtn).clone();
	    	shareBtn = $(billShareBtn).clone();

	    	$(shareBtn).attr({
	    		"data-bill_id":res.idbills,
	    		"data-sus_id":localbill_id,
	    		"data-shared":res.shared
	    	});

	    	$(openBtn).attr({
	    		"data-bill_id":res.idbills,
	    		"data-sus_id":localbill_id
	    	});

	    	$(delBtn).attr({
	    		"data-sus_id":localbill_id,
	    		"data-bill_id":res.idbills
	    		
	    	});
	    	
	    	var waiterName = typeof res.waiter_name !=="undefined" ? res.waiter_name : suspendedBills[localbill_id].waiter_name;
	    	var billDate = new Date(Date.parse(res.date));
	    	
	    	var Bill_li = $("<li>").html(((res.shared>0) ? "<i class='fa fa-globe'></i>" :"" )+"<b>#"+res.idbills+"</b> ["+billDate.getHours()+":"+billDate.getMinutes()+"] <i class='fa fa-clock-o'></i> ").append($(delBtn)).append($(openBtn)).append($(shareBtn)).addClass("bill_item bill_item_"+res.idbills);

			var existingWaiterLi = $(suspList).children("li.waiter_"+res.waiter_id);

			if($(existingWaiterLi).length ==0){
				//create new li for the waiter
				var waiter_li = $("<li class='waiter_"+res.waiter_id+"'>").html("<a href='#' class='waiter_btn'> <i class='fa fa-plus-square-o'></i> "+waiterName+" (<span>1</span>)</a>");
				$(waiter_li).append($("<ul>").html($(Bill_li)));

				$(suspList).prepend(waiter_li);
			}else {
					
				var counter = parseInt($(existingWaiterLi).find("a > span").html());
				
				$(existingWaiterLi).find("a > span").html(counter+1);
				$(existingWaiterLi).children("ul").prepend(Bill_li);
			}

			countSuspendedBills();
	    }

	    // update mode is used to add products to a saved bill , which will later be updated
	    function addProductToBill(product,enableUpdateMode)
	    {
	    	enableUpdateMode = typeof enableUpdateMode === 'undefined' ? false : true;
	    	
	    	
	    	//check if product exists and add quantity only
	    	if($("[data-prodid="+product.id+"]").length>0)
	    	{
	    		var row =$("tr[data-prodid="+product.id+"]");
	    		
	    		qtybox = $(row).find(".qty_box");
	    		oldValue =parseInt($(qtybox).val());

	    		$(qtybox).val(oldValue+1).change();
	    		return 0;
	    	}

	    	var objID  = billItems.length;

			var prod_qty = NumericBox(product.qty);

			var row = $("<tr>").attr("data-oid",objID).attr("data-prodid",product.id);;

			product.total = product.price * product.qty;
		    
		    /** Columns **/
			var nameCol = $("<td>").html(product.name);
			var priceInput = $("<input type='text' class='price-field' />");
			$(priceInput).val(product.price);
			var priceCol = $("<td>").html($(priceInput));
			var qtyCol = $("<td>").html($(prod_qty));
			var totalCol = $("<td>").html($("<input type='text' readonly='readonly' class='row_total row_total"+objID+"'>").val(product.total));
		    var deleteBtn = $("<button data-prod_id='"+product.id+"' data-id='"+objID+"' class='delete_btn'>").html("<i class='fa fa-trash'></i>");
            

            billTotal += product.total;
            taxTotal = (billTotal*options.taxPercent)/100;

            billItems.push(product);
            
			$(row).append(nameCol);
			$(row).append(priceCol); 
			$(row).append(qtyCol);
			$(row).append(totalCol);
			$(row).append($("<td>").html(deleteBtn));
			
			$(table).append($(row));

			$(billTotalField).val(billTotal);
			$(taxField).val(taxTotal);

			if(updateMode && enableUpdateMode){
				DBUpdate.newItems.push(product);
			}
	    }

		return this;
	}


	function NumericBox(qty)
	{
		var wrapper = $('<span class="num-field">');
		var inpt = $('<input type="text" class="qty_box" />');
		var upbtn =$('<button class="up-btn">');
		var downbtn = $('<button class="down-btn">');

		$(downbtn).bind({
			click:function(e){
				e.preventDefault();
				prevVal = parseFloat($(inpt).val());
				if(prevVal==1)return false;
				$(inpt).val(prevVal-1).change();
			}
		})

		$(upbtn).bind({
			click:function(e){
				e.preventDefault();
				prevVal = parseFloat($(inpt).val());
				$(inpt).val(prevVal+1).change();
			}
		})
		$(inpt).val(qty);
		$(upbtn).html('<i class="fa fa-caret-up"></i>');
		$(downbtn).html('<i class="fa fa-caret-down"></i>');

		$( wrapper).append(inpt).append(upbtn).append(downbtn);

		return $(wrapper);
	}


	function Request(_options)
	{
		var options = {
			url : typeof _options.url == "undefined" ? "" : _options.url ,
			method: typeof _options.method == "undefined"  ? "GET" :  _options.method,
			data : typeof _options.data == "undefined" ? "" : _options.data 
		};

		var ReturnedData = "";

		var ds = $.ajax({

			url:options.url,
			data: options.data,
			type: options.method,
			statusCode : {
				404 : function(){
					ualert.error("Page not found");
				},
				401 : function(){
					ualert.error("Authentication Error");
				}
			},
			error:function()
			{
				ualert.error("Request Error");
			}
		});

		var dv="";

		ds.success(function(data){
			dv = data
		});	
	}


}(jQuery));


	var ualert = function(){};

	ualert.init = function(){

		var alertBox = $('<div class="ualert">');
		var alertIcon = $('<i class="fa">');
		var alertContent = $('<div class="inner_content">');

		$(alertBox).append(alertIcon);
		$(alertBox).append(alertContent)
		
		return alertBox;
	}

	ualert.error = function(message)
	{
		okBtn = $('<button class="ok-btn">').html("OK"); 
		var BgColor = "#c0392b";
		var foreColor = "#000";
		box = this.init();

		$(document).ready(function(){

			$(box).css({
				"background":BgColor,
				"color":foreColor
			}).draggable();

			$(okBtn).click(function(e){
				e.preventDefault();
				$(this).parents(".ualert").fadeOut(500,function(){
					$(this).remove();
				})
			});

			$(box).children('.inner_content').append(message); //message
			$(box).children('.fa').addClass('fa-exclamation-triangle');
		    $(box).append(okBtn);

			$("body").prepend(box);

		});	
	}


	ualert.warning  = function (message){

		okBtn = $('<button class="ok-btn">').html("OK"); 
		var BgColor = "#f39c12";
		var foreColor = "#fff";
		box = this.init();

		$(document).ready(function(){

			$(box).css({
				"background":BgColor,
				"color":foreColor
			}).draggable();

			$(box).children('.inner_content').append(message); //message
			$(box).children('.fa').addClass('fa-info-circle');
		    $(box).append(okBtn);

			$("body").prepend(box);

			$(okBtn).click(function(e){
				e.preventDefault();
				$(this).parents(".ualert").fadeOut(500,function(){
					$(this).remove();
				})
			})

			setTimeout(function(){
				$(box).fadeOut(800)
			},3500)

		});
	}

	ualert.success = function(message)
	{
		okBtn = $('<button class="ok-btn">').html("OK"); 
		var BgColor = "#2ecc71";
		var foreColor = "#fff";
		box = this.init().clone(true);

		$(document).ready(function(){

			$(box).css({
				"background":BgColor,
				"color":foreColor
			}).draggable();

			$(box).children('.inner_content').append(message); //message
			$(box).children('.fa').addClass('fa-info-circle');
		    $(box).append(okBtn);

			$("body").prepend(box);

			$(okBtn).click(function(e){
				e.preventDefault();
				$(this).parents(".ualert").fadeOut(500,function(){
					$(this).remove();
				})
			})

			setTimeout(function(){
				$(this.box).fadeOut(800,function(){
					$(this).remove();
				})
			},3500)

		});	
	}

	function printBill(id)
	{
		$(document).ready(function(){
			var destUrl ="/POS/Bills/PrintBill/"+id+((typeof JSObj !== "undefined") ? "?xml": "");
			
			$.ajax({
				url :destUrl,
				type : "get",
				success : function(data){

					if(data.length ==1){
						ualert.error("Error printing the bill , Please use bills table to re-print the bill");
						return;
					}
					if (typeof JSObj === "undefined") {
					    $(".print_container").html("");
					    $(".print_container").prepend(data).append(data);
					}

					if(data.length>1)
					{
					    if (typeof JSObj ==="undefined") {
					        window.print();
					    } else {
					        JSObj.PrintXmlBill(data);
					    }
					}else {
						ualert.error("Unable to print the bill , Please contact your system administrator");
					}
					

					//reset 
					$(".print_container").html("");
				},
				error : function(){
					alert("Unable to initiate the printer");
				},
				statusCode: {
					    401: function() {
					      ualert.error("Your session has expired please logout and login again !");
					      setTimeout(function(){
					      	window.location.reload();
					      },3000)
					    }
					}
			})


		})
	}
