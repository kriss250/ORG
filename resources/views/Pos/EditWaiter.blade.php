@extends("Pos.master")

@section("contents")

 <h2>Update Waiter</h2>

<p class="page_info"><i class="fa fa-info-circle"></i> Please use the form below to register new waiters.</p>

<br>
<form name="reg_form" style="max-width:500px;" id="waiter_reg_form" action="{{ action('WaiterController@update',$waiter->idwaiter)}}" method="post">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input name="_method" type="hidden" value="PUT">
  <div class="form-group">
    <label for="exampleInputEmail1">Firstname</label>
    <input type="text" name="firstname"  value="{{$waiter->firstname}}" class="form-control" placeholder="Firstname">
  </div>

  <div class="form-group">
    <label for="exampleInputEmail1">Lastname</label>
    <input type="text" name="lastname" value="{{$waiter->lastname}}" class="form-control" autocomplete="off" placeholder="Lastname">
  </div>


<div class="form-group">
    <label for="exampleInputEmail1">Nickname</label>
    <input type="text" name="waiter_name" value="{{$waiter->waiter_name}}" class="form-control" placeholder="Nickname">
 </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Reset PIN</label>
        <input type="number" name="pin" placeholder="New PIN" class="form-control" />
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
						ualert.success("Waiter successfully created");
						//location.refresh();
					}


				},
				error : function(){
					ualert.error("Error saving Waiter");
				}
			}).done(function(){
				$(form).children("button[type='submit']").removeAttr("disabled").html("Submit");
			})

		})
	})
</script>

@stop