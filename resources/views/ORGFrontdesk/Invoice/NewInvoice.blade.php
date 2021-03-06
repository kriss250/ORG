<?php 

$show = count($invoice_data) > 0 ? true : false;
if($show){
	$time = strtotime($invoice_data[0][0]->date);
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <meta http-equiv="X-UA-Compatible" content="IE=10" >
</head>
<body style="max-width:10in;margin:auto">
    {!! HTML::style('assets/css/vendor/bootstrap.min.css') !!}
    {!! HTML::style('assets/css/vendor/font-awesome.min.css') !!}
    {!! HTML::script('assets/js/vendor/jquery/jquery-1.11.2.min.js') !!}
    {!! HTML::script('assets/js/vendor/accounting/accounting.js') !!}

    <style>
        table {
            width:100%;
           box-sizing:padding-box;
            padding:15px;
            margin:10px auto;
        }

        .invoice {
            margin-top:20px
        }
        table .footer-row td {
            text-align:right;
            padding:3px 0px
        }

        table .footer-row td:first-child {
            border:none;
            padding-right:10px
        }

        table .footer-row:last-child td {
            font-weight:bold
        }

        table .footer-row:last-child td:last-child {
            border-bottom:1px solid;
            border-top:1px solid
        }

        table th {
            padding:3px 7px;text-align:center;
            border:1px solid;
        }

        table td {
            border-right:1px solid;
            border-left:1px solid;
            padding:7px 0;
        }
        table td:nth-child(1){
            width:11px
        }

        
        table td:nth-child(2){
            width:350px
        }

        table td:nth-child(3){
            width:70px
        }
            table td:last-child {
                width: 100px;
            }
        table input[type=text]{
            border-bottom:1px solid #C3C3C3;
            border-top:none;
            border-left:none;
            background:linear-gradient(to bottom,#FFF,#ECECEC);
            width:100%;
            padding:5px 10px
        }


        table .items_row.last {
           border-bottom:1px  solid !important;
        }

		address input[type=text]{
		  border-bottom: 1px dotted;
		  border-left: none;
		  border-right: none;
		  border-top: none;
		  padding: 3px
		}

		@media print {
			input[type="text"] {
			  background: none !important;;
			  border: none !important;
	        }

			address input[type="text"]{
				margin: 0;
				padding: 0;
				height: 12px
			}


			button {
			  display: none !important
			}
		}

	   .footer {
		  border: 1px solid gray;
		  display: table;
		  padding: 10px 18px;
		  font-size: 13px
		}

		.footer h4 {
		  font-size: 13px;
		  border-bottom: 1px dotted gray;
		  padding-bottom: 3px
		}
    </style>

    <script>
    $(document).ready(function(){

    	accounting.settings = {
            currency: {
                symbol: "",   // default currency symbol is '$'
                format: "%v%s", // controls output: %s = symbol, %v = value/number (can be object: see below)
                decimal: ".",  // decimal point separator
                thousand: ",",  // thousands separator
                precision: 0   // decimal places
            },
            number: {
                precision: 0,  // default precision on numbers is 0
                thousand: ",",
                decimal: "."
            }
        }

    	$("#invoice").submit(function(e){
    		e.preventDefault();

    		$("[name='submit']").attr("disabled","disabled");

    		var data = $("#invoice").serialize();

    		$.ajax({
    			"url":"/ORGFrontdesk/Invoice?user={{ isset($_GET['user']) ? $_GET['user'] : '' }}",
    			"type":"post",
    			"data":data,
    			"success":function(id){
    				$("#invoice_id").html(id);
    				$("[name='submit']").removeAttr("disabled");
    				window.print();
    			},
    			"error": function(e,ec,t){
    				alert(t);
    			}
    		})
    	})

    	$(".uprice").on("change",function(){
    		var id = $(this).attr("data-row");
    		var qty = parseFloat($("[name='qty"+id+"']").val());
    		var uPrice = parseFloat($("[name='uprice"+id+"']").val())
    		$("[name='itemtotal"+id+"']").val(qty*uPrice).change();
    	})

    	$(".qty").change(function(){

    		var id = $(this).attr("data-row");
    		var uPrice = parseFloat($("[name='uprice"+id+"']").val());
    		var qty = parseFloat($(this).val());

    		if(!isNaN(uPrice) && !isNaN(qty)){
               $("[name='itemtotal"+id+"']").val(qty*uPrice).change();
            }else {
            	$("[name='itemtotal"+id+"']").val(0);
            }
    	})

    	$(".itemtotal").on('change',function(){
    		
    		var totals = $(".itemtotal");
			var total = 0;
    		$.each(totals, function( index, value ) {

              var colval = parseFloat($(value).val());
              if(isNaN(colval)){
              	colval = 0;
	          }

              total += colval;
				
			});

	                 $(".subtotal").val(accounting.formatMoney(total)).change();

    	})


    	$(".subtotal").change(calcTotal);
    	$(".tax").change(calcTotal)
    })

function calcTotal(){

				var tax = parseFloat($(".tax").val());
				var subtotal = parseFloat( accounting.unformat($(".subtotal").val()));

				if(isNaN(tax)){
					tax = 0;
				}

				if(isNaN(subtotal)){
					subtotal = 0;
				}

				$(".total").val(accounting.formatMoney(subtotal-tax));
}

    </script>


    {!! Form::open(["url"=>"/ORGFrontdesk/Invoice","id"=>"invoice"]); !!}
    <input type="hidden" name="invoice_type" value="{{ isset($_GET['proforma']) ? "proforma" : "invoice" }}">
    <div class="invoice">
    <header class="invoice_header container-fluid">

        <div class="col-sm-7 col-xs-7" style="padding-top:15px">

            <div class="row">
            <div class="col-md-2 col-xs-2">
                <img style="max-width:100%;" width="100" src="data:image/jpeg;base64,<?php echo base64_encode( $hotelInfo[0]->logo ); ?>"/>
            </div>
            <div style="padding:0" class="col-md-7 col-xs-7">
                <h3 style="margin:0"> {{ $hotelInfo[0]->hotel_name }}</h3>
            <p style="margin-top:0px;color:#8a8a8a;font-size:13px">{{ $hotelInfo[0]->moto }}</p>
            </div>
            </div>
            
            

            <br />
            <address style="line-height:.7;font-size:13px">
            <p> {{ $hotelInfo[0]->country }} - {{ $hotelInfo[0]->city }}  </p>
            <p> {{ $hotelInfo[0]->address_line1 }} </p>
            <p> Phone : {{ $hotelInfo[0]->phone1}} / {{ $hotelInfo[0]->phone2}}</p>
            </address>
            
            <h4 style="margin-top:15px">TO : </h4>
            <address style="line-height:.7;font-size:13px">
            <p><input type="text" placeholder="Company Name" name="company_name" value= "{{ $show ? $invoice_data[0][0]->company_name : '' }}" /></p>
            <p><input type="text" placeholder="Country" name="country" value= "{{ $show ? $invoice_data[0][0]->country : '' }}" /> - <input type="text" placeholder="City" name="city" value="{{ $show ? $invoice_data[0][0]->city : '' }}"  </p>
            <p><input type="text" name="address1" placeholder="Address" value="{{ $show ? $invoice_data[0][0]->address_line : '' }}" /> </p>
            <p> Phone : <input type="text" name="phone" placeholder="Phone" value="{{ $show ? $invoice_data[0][0]->phone : '' }}"  /></p>
            </address>
        </div>

        <div class="col-sm-5 col-xs-5 text-right">
            <h1>{{ isset($_GET['proforma']) ? "PROFORMA" : "INVOICE" }} </h1>
            <br />
            <p> {{ isset($_GET['proforma']) ? "PROFORMA" : "INVOICE" }}  # : {{ ($show) ? date("y",$time) :  date("y") }}/<span id="invoice_id">{{ ($show) ? $invoice_data[0][0]->idinvoices : '...' }} </span></p>
            <p>Date  :  {{ ($show) ? date(\ORG\Dates::DSPDATEFORMAT,$time)  : \ORG\Dates::WORKINGDATE(false)  }}</p>
        </div>
    </header>

    <div class="container-fluid" style="padding:25px">
        
    <table>
        <thead>
            <tr>
                <th>QTY</th><th>DESCRIPTION</th><th>U. PRICE</th><th>TOTAL</th>
            </tr>
        </thead>

        <tbody>
            <?php $rows = 7; $x = 0; ?>

            @for($i = 0;$i<$rows;$i++)
            <tr class="items_row{{ ($i==6) ? ' last' : ''}}">
           
                <td><input type="text" value="{{ ($show && isset($invoice_data[1][$x]->quantity)) ? $invoice_data[1][$x]->quantity : '' }}" class="qty" name="qty{!!$i!!}" data-row="{!!$i!!}" /> </td>
                <td><input type="text" value="{{ ($show && isset($invoice_data[1][$x]->description)) ? $invoice_data[1][$x]->description: '' }}" name="desc{!!$i!!}" data-row="{!!$i!!}" /></td>
                <td><input type="text" value="{{ ($show && isset($invoice_data[1][$x]->uprice)) ? $invoice_data[1][$x]->uprice : '' }}" class="uprice" data-row="{!!$i!!}"  name="uprice{!!$i!!}" /></td>
                <td><input readonly type="text" value="{{ ($show && isset($invoice_data[1][$x]->row_total)) ? $invoice_data[1][$x]->row_total : '' }}" class="itemtotal" name="itemtotal{!!$i!!}" data-row="{!!$i!!}" /></td>
            </tr>

            <?php $x++; ?>

            @endfor

            <tr class="footer-row">
                <td colspan="3">SUBTOTAL</td><td><input readonly value="{{ $show ? $invoice_data[0][0]->sub_total : '0' }}" class="subtotal" name="subtotal" type="text" /></td>
            </tr>

            <tr class="footer-row">
                <td colspan="3">TAX</td><td><input readonly class="tax" type="text" value="{{ $show ? $invoice_data[0][0]->tax : '0' }}" name="tax" /></td>
            </tr>

            <tr class="footer-row">
                <td colspan="3">TOTAL</td><td><input readonly type="text" value="{{ $show ? $invoice_data[0][0]->total : '0' }}" class="total" name="total" /></td>
            </tr>

        </tbody>

    </table>

<div class="footer">
        <p><b>TIN/VAT</b> {{ $hotelInfo[0]->TIN }}</p>
        <div class="bank_accounts">
            <h4>Bank Accounts</h4>
             @foreach($banks as $bank)
            {{ $bank->bank_name." ". $bank->account_code." /"  }}

            @endforeach
        </div>
</div>
        </div>

        {!! (!$show) ? '<button style="margin:10px auto;display:block" name="submit" type="submit" class="btn btn-success">Save & Print</button>' : '' !!}
    </div>

    </form>


    {!! ($show) ? '<button onclick="window.print()" style="margin:10px auto;display:table;" class="btn btn-info">Re-print</button> ' : '' !!}
</body>
</html>




