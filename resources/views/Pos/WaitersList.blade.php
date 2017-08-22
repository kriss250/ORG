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
		"ajax": '<?php echo action("WaiterController@index")."?json";  ?>',
		"columnDefs": [ {
            "targets": -1,
            "data": null,
            "defaultContent": $(buttons).html()
        }]
	});


    $("#table").on("click",".edit_btn",function(e){
		e.preventDefault();
		var id = $(this).parent().parent().children('td').html();

		if(isNaN(parseInt(id))){
			//not a number
		}else {
		    uri = decodeURI("{{ action('WaiterController@edit') }}");
		    uri = uri.replace('{Waiters}', id);
		   window.location.href = uri;
		}
	});

})
	
</script>


 <h2>Users List </h2>

<p class="page_info"><i class="fa fa-info-circle"></i> List of allow users active an inactive.</p>


<table id="table" class="display" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th>ID</th>
			<th>Nickname</th>
			<th>Firstname</th>
			<th>Lastname</th>
			<th>Date</th>
			<th>Action</th>
		</tr>
	</thead>

</table>


@stop