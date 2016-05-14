@extends("Pos.master")

@section("contents")

<script type="text/javascript">
$(document).ready(function(){

  	var editBtn = $("<button class='action_btn edit_btn'><i class='fa fa-edit'></i> </button>");
  	var deleteBtn = $("<button class='action_btn delete_btn'><i class='fa fa-trash-o'></i> </button>");
    var buttons= $("<div>");
   

    $(buttons).append(editBtn);
    $(buttons).append(deleteBtn);
    
	$('#table').dataTable({
		"sDom": 'T<"clear">lfrtip',
		 "tableTools": {
            "sSwfPath": "/assets/js/vendor/datatables/extensions/tableTools/swf/copy_csv_xls_pdf.swf"
        },
		"processing": true,
		"serverSide": true,
		"ajax": '<?php echo action("ProductsCategoryController@index")."?json";  ?>',
		"columnDefs": [ {
            "targets": -1,
            "data": null,
            "defaultContent": $(buttons).html()
        }]
	});
})


	
</script>

<h2>Products</h2>

<table id="table" class="display" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>

			<th>Description</th>
			<th>Date</th>
			<th>Action</th>
		</tr>
	</thead>

	<tfoot>
		<tr>
		<th>ID</th>
			<th>Name</th>

			
			<th>Description</th>
			<th>Date</th>
			<th>Action</th>
		</tr>
	</tfoot>
</table>

@stop