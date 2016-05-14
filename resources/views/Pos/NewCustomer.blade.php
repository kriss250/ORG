@extends("Pos.master")

@section("contents")

 <h2>New Customer </h2>

<p class="page_info"><i class="fa fa-info-circle"></i> Please use the form below to register new customers.</p>

<br>
<form name="reg_form" style="max-width:500px;" id="user_reg_form" action="{{ action('CustomersController@store')}}" method="post">
<input type="hidden" name="_token" value="{{ csrf_token() }}">

  <div class="form-group">
    <label for="exampleInputEmail1">Firstname</label>
    <input type="text" name="firstname"  class="form-control" placeholder="Firstname">
  </div>

  <div class="form-group">
    <label for="exampleInputEmail1">Lastname</label>
    <input type="text" name="lastname" class="form-control" autocomplete="off" placeholder="Lastname">
  </div>


<div class="form-group">
    <label for="exampleInputEmail1">Nickname</label>
    <input type="text" name="nickname" class="form-control" placeholder="Nickname">
  </div>

 

<div class="form-group">
    <label for="exampleInputEmail1">Email</label>
    <input type="email" name="email" required="" class="form-control" placeholder="Email">
  </div>

<div class="form-group">
    <label for="exampleInputEmail1">Phone</label>
    <input type="text" name="phone" class="form-control" placeholder="Phone">
  </div>


    <div class="form-group">
    <label for="exampleInputEmail1">Address</label>
    <input type="text" name="address" class="form-control" placeholder="Address">
  </div>

<div class="form-group">
    <label for="exampleInputEmail1">Company</label>
    <input type="username" name="company" class="form-control" placeholder="Company Name">
  </div>

  <button type="submit" class="btn btn-default">Submit</button>
</form>

<label>Favorite</label>
<input type="checkbox" class="form-control" name="favorite" />

<script type="text/javascript">
	$(document).ready(function(){
		$("#user_reg_form").submit(function(e){
			var form = $(this);
			e.preventDefault();


			$(this).children("button[type='submit']").attr("disabled","disabled").append("...");
			var data = $(this).serialize();

			$.ajax({
				url : $(form).attr("action"),
				type:"post",
				data : data,
				success: function (data){
					try {
						jData = JSON.parse(data);
					}catch(ex){
						ualert.error(ex);
						return;
					}

					if(jData.errors != null && Object.keys(jData.errors).length > 0)
					{
						str = "";
						$.each(jData.errors,function(index,value){
							str += value+"<br/>";
						});
						ualert.error(str);
					}

					if(jData.success ==1){
						ualert.success("Customer successfully created");
						location.refresh();
					}


				},
				error : function(){
					ualert.error("Error saving Customer");
				}
			}).done(function(){
				$(form).children("button[type='submit']").removeAttr("disabled").html("Submit");
			})

		})
	})
</script>

@stop