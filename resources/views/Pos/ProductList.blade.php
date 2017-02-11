@extends("Pos.master")

@section("contents")

<script type="text/javascript">
$(document).ready(function(){

  	var editBtn = $("<button class='action_btn edit_btn'><i class='fa fa-edit'></i> </button>");
  	var deleteBtn = $("<button class='action_btn delete_btn'><i class='fa fa-trash-o'></i> </button>");
  	var favoriteBtn  = $("<button class='action_btn fav_btn'><i class='fa fa-heart'></i></button>");
    var buttons= $("<div>");

    $(buttons).append(editBtn).append(deleteBtn).append(favoriteBtn);
    
	var Table = $('#table').dataTable({
		"sDom": 'T<"clear">lfrtip',
		 "tableTools": {
            "sSwfPath": "/assets/js/vendor/datatables/extensions/tableTools/swf/copy_csv_xls_pdf.swf"
        },
		"processing": true,
		"serverSide": true,
		"iDisplayLength": "100",
		"aoColumns": [
           { "bSearchable": false }, null, { "bSearchable": false }, { "bSearchable": false }, { "bSearchable": false }, { "bSearchable": false }, { "bSearchable": false }, { "bSearchable": false }, { "bSearchable": false }
		] ,
		"ajax": '<?php echo action("ProductsController@index")."?json";  ?>',
		"columnDefs": [ {
            "targets": -1,
            "data": null,
            "defaultContent": $(buttons).html()
        }]
	}).api();

	$("#table").on("click",".edit_btn",function(e){
		e.preventDefault();
		var id = $(this).parent().parent().children('td').html();

		if(isNaN(parseInt(id))){
			//not a number
		}else {
			window.location.href = "{{ action('ProductsController@create') }}?id="+id;
		}
	});



	$("#table").on("click", ".delete_btn", function (e) {
	    e.preventDefault();
	    var id = $(this).parent().parent().children('td').html();

	    if (isNaN(parseInt(id))) {
	        //not a number
	    } else {
	        var Url = "{{ action('ProductsController@destroy') }}";

	        Url = decodeURI(Url);
	        Url = Url.replace('{Products}', id);

	        $.ajax({
	            url: Url,
	            "type": "delete",
	            "data": { "_token": $("meta[name='csrf-token']").attr("content") },
	            "success": function (data) {
	                if (data == "1") {
	                    ualert.success("Product deactivated !");
	                } else {
	                    ualert.error("Error deactivating Product");
	                }
	            }
	        })
	    }
	});

	$("#table").on("click", ".fav_btn", function (e) {
	    e.preventDefault();
	    var id = $(this).parent().parent().children('td').html();

	    if (isNaN(parseInt(id))) {
	        //not a number
	    } else {

	        var Url = "{{action('ProductsController@markAsFavorite') }}";
	        state = "1";
	        Url = decodeURI(Url);
	        Url = Url.replace('{Product}', id);
	        Url = Url.replace('{state}', state);
	        $.ajax({
	            url: Url,
	            "type": "get",
	            "data": { "_token": $("meta[name='csrf-token']").attr("content") },
	            "success": function (data) {
	                data = JSON.parse(data);
	                
	                if (data[0] > 0) {
	                    ualert.success("Product Marked as favorite !");
	                } else {
	                    ualert.error("Error marking product");
	                }
	            }
	        })
	    }
	})
})

	
</script>

<h2>Products</h2>
<p class="page_info"><i class="fa fa-info-circle"></i> Please use the table below to navigate or filter the results. You can download the table as csv, excel and pdf.</p>

<table id="table" class="display" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Category</th>
			<th>Sub Category</th>
			<th>Price</th>
            <th>Stock Code</th>
			<th>Description</th>
			<th>Date</th>
			<th>Action</th>
		</tr>
	</thead>

	<tfoot>
		<tr>
		<th>ID</th>
			<th>Name</th>
			<th>Category</th>
			<th>Sub Category</th>
			<th>Price</th>
            <th>Stock Code</th>
			<th>Description</th>
            
			<th>Date</th>
			<th>Action</th>
		</tr>
	</tfoot>
</table>

@stop