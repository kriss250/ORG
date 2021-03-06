@extends("Pos.master")

@section("contents")

<script type="text/javascript">
$(document).ready(function(){

  	var editBtn = $("<button class='action_btn open_btn'><i class='fa fa-eye'></i> </button>");
  	var deleteBtn = $("<button class='action_btn delete_btn'><i class='fa fa-trash-o'></i> </button>");
    var payBtn = $("<button style='color:#5AC53A' class='action_btn pay_btn'><i class='fa fa-pencil'></i> </button>"); 
    var buttons= $("<div>");
   

    $(buttons).append(editBtn);
    $(buttons).prepend(payBtn);
    <?php if( Auth::user()->level > 4 ){ ?>
    $(buttons).append(deleteBtn);

    <?php } ?>
    
	table = $('#table').dataTable({
		"sDom": 'T<"clear">lfrtip',
		
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            totalSett = api
                .column( 5 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                } );

               totalTax = api
                .column( 3 , { page: 'current'})
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                } );
 
            // Total over this page
            billsTotal = api
                .column(2, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 2 ).footer() ).html(
                billsTotal 
            );

            $( api.column( 5 ).footer() ).html(
                totalSett
            );

            $( api.column( 3 ).footer() ).html(
                totalTax
            );
        
        },
		 "tableTools": {
            "sSwfPath": "/assets/js/vendor/datatables/extensions/tableTools/swf/copy_csv_xls_pdf.swf",
            "aButtons": [
                    "copy",
                    "csv",
                    "xls",
                    {
                        "sExtends": "pdf",
                        "sTitle": "Resto-Bar Bills",
                        "sPdfMessage": "Summary Info",
                        "sPdfOrientation": "landscape"                        
                    },
                    "print"
                ]
        },
        "order": [[ 0, "desc" ]],
		"processing": true,
		"serverSide": true,
		"ajax": '<?php echo action("BillsController@assignedList")."?json";  ?>',
		"columnDefs": [ {
            "targets": -1,
            "data": null,
            "defaultContent": $(buttons).html()
        }]
          
	});

	console.log(table);
})

$(document).ready(function(){

	$.ajaxSetup({
		 headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content") }
    });

	$("#table").on('click','.delete_btn',function(e){
		e.preventDefault();
		var Row = $(this).parents('tr') ;
        //
        var aData = table.fnGetData(Row);
        var id  = aData[0];

        $.ajax({
        	url:"<?php echo action("BillsController@destroy");  ?>",
        	data: {id: id,retain:1},
        	type:"delete",
        	success : function(data){
        		table.fnDeleteRow( Row );
        	},
        	error:function(){
        		ualert.error("Error Deleting Bill");
        	}
        })
	});


	$("#table").on('click','.open_btn',function(e){

		var billDiv = $("<div class='inline_bill'>");
		var printBtn = $("<button class='print_btn'>").html("<i class='fa fa-print'></i> Print");
		var billTable = $("<table>");
        $(".print_container").html("");

		var Row = $(this).parents('tr') ;
        //
        var aData = table.fnGetData(Row);
        var id  = aData[0];

        $.ajax({
        	url:"<?php echo action('BillsController@getBillItems'); ?>",
        	type:"get",
        	data: {billID : id},
        	success:function(data){
        		try {
        			jData = JSON.parse(data);

        			$.each(jData,function(index,value){
        				var row = $("<tr>");

        				$(row).append("<td>"+value.product_name+"</td>");
        				$(row).append("<td>"+value.unit_price+"</td>");
        				$(row).append("<td>"+value.qty+"</td>");

        				$(billTable).prepend(row);
        			});

        			$(billDiv).prepend("<p class='text-center'>Bill #"+id+" <i class='close_btn fa fa-times'></i></p>");
        			$(billDiv).append(billTable).append(printBtn).draggable();

        			$(Row).append(billDiv);
        		}catch(ex){
        			ualert.error("Data format error");
        			return;
        		}



        	}
        })
	});


    $("#table").on('click','.pay_btn',function(e){
        var Row = $(this).parents('tr') ;
        //
        var aData = table.fnGetData(Row);
        var id  = aData[0];
        var room = aData[1];
        var amount = aData[2];
        var form = $("<form method='POST' id='pay_form' action='{{ action('BillsController@payAssignedBill') }}'>");
        var submitBtn = $("<input type='submit' value='Pay'>");
        var idField = $("<input type='hidden' name='billID'>").val(id);
        var tokenField = $('{!! csrf_field() !!}');
        var pay_field = $("<input type='text' name='amount_paid'>").val(amount);

        $(form).append("<label>Amount Paid</label>").append(pay_field).append(idField);
        $(form).append('<label>Pay. Method</label><select id="pay_method" name="method"><option>Cash</option><option>Bank Card</option><option>Check</option><option>Bon</option></select>').append(submitBtn).append(tokenField);

        var paybox = $("<div class='inline_pay_box inline_bill'>");
        $(paybox).append("<p class='text-center'>Room : "+room+"</p>").append("<span class='text-center'>Bill ID# : "+id+"</span>").append("<span class='text-center'>Due amount : "+amount+"</span>").append(form).draggable();
        $(this).parent().append(paybox);

    });


	$("#table").on('click','.close_btn',function(e){
		$(this).parents(".inline_bill").remove();
	})

    $("#table").on("submit",'#pay_form',function(e){
        e.preventDefault();
        var data = $(this).serialize();
        var form = $(this);

        if( isNaN( parseInt($("input[name='amount_paid']").val()) ) ){
            ualert.error("Invalid amount value");
            return;
        }

        $.ajax({
            url:$(form).attr("action"),
            type:"POST",
            data : data,
            success : function(data){
                data = JSON.parse(data);
                if(data["pay"] =="1" || data["errors"].length==0)
                {   
                    ualert.success("Payment Saved");
                    window.countSales();
                   table.api().ajax.reload();
                }else {
                    ualert.error(data["errors"][0]);
                }
            }
        });
    });


    $("#table").on("click",'.print_btn',function(e){
        e.preventDefault();

        var Row = $(this).parents('tr') ;
        var aData = table.fnGetData(Row);
        var id  = aData[0];

        window.printBill(id);
    });
})

</script>

<h2>Bills Assigned to Rooms </h2>
<p class="page_info"><i class="fa fa-info-circle"></i> Please use the table below to navigate or filter the results. You can download the table as csv, excel and pdf.</p>
<table id="table" class="display" cellspacing="0" width="100%">
	<thead>
		<tr style="font-size:12px !important">
			<th>ID</th>
			<th>Room</th>
			<th>Bill Total</th>
			<th>Tax</th>
			<th>Paid/Change</th>
			<th>Settl.</th>
            <th>User</th>
			<th>Waiter</th>
			<th>Pay Date</th>
			<th> Bill Date</th>
			<th> Action </th>
		</tr>
	</thead>
<tfoot>
	<th>TOTAL</th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
</tfoot>
</table>

@stop