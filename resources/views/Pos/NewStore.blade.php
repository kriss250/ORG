@extends("Pos.master")

@section("contents")

 <h2>New Store </h2>

<p class="page_info"><i class="fa fa-info-circle"></i> Please use the form below to register new stores.</p>

<br>
<form name="reg_form" style="max-width:500px;" id="waiter_reg_form" action="{{ action('StoreController@store')}}" method="post">
<input type="hidden" name="_token" value="{{ csrf_token() }}">

  <div class="form-group">
    <label for="store_name">Store Name</label>
    <input type="text" name="name"  class="form-control" placeholder="Name of the store">
  </div>
 

  <button type="submit" class="btn btn-default">Submit</button>
</form>


<script type="text/javascript">
	$(document).ready(function(){
		$("#waiter_reg_form").submit(function(e){
			var form = $(this);
			e.preventDefault();


			$(this).children("button[type='submit']").attr("disabled","disabled").append("...");
			var data = $(this).serialize();

			$.ajax({
				url : $(form).attr("action"),
				type:"post",
				data : data,
				success: function (data){
				   if(data == 1)
				   {
				   	ualert.success("New store saved successuly");
				   }else {
				   	ualert.error("Error Creating store");
				   }
				},
				error : function(){
					ualert.error("Server Error");
				}
			}).done(function(){
				$(form).children("button[type='submit']").removeAttr("disabled").html("Submit");
			})

		})
	})
</script>

@stop