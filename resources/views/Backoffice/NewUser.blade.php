@extends('Backoffice.Master')
@section("contents")

<div class="single-page-contents">
   
    <h2 style="margin-top:-5px;">New User </h2>
    <p class="page_info"><i class="fa fa-info-circle"></i> Please use the form below to register new users. Assign levels from 1 - 10.</p>
    <br>
    <form name="reg_form" id="user_reg_form" style="max-width:500px;" class="ajaxorm" action="{{ action('UsersController@store')}}" method="post">
        <input type="hidden" name="_token" value="{{csrf_token() }}">
        <div class="form-group">
            <label for="exampleInputEmail1">Firstname</label>
            <input type="text" name="firstname" class="form-control" placeholder="Firstname">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">Lastname</label>
            <input type="text" name="lastname" class="form-control" autocomplete="off" placeholder="Lastname">
        </div>

        <div class="form-group">
            <label for="exampleInputEmail1">Username</label>
            <input type="username" name="username" class="form-control" id="exampleInputEmail1" placeholder="Username">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">Group</label>
            <select class="form-control" name="level">
                <option value="7">Supervisor</option>
                <option value="8">Finance Dpt</option>
                <option value="9">Manager</option>
                <option value="10">Admin</option>
            </select>
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">Password</label>
            <input type="password" name="password" class="form-control">
        </div>

        <div class="form-group">
            <label for="exampleInputPassword1">Repeat Password</label>
            <input type="password" name="password2" class="form-control" id="exampleInputPassword1" placeholder="Password">
        </div>

        <button type="submit" class="btn btn-default">Submit</button>
    </form>
    <div>


        <script type="text/javascript">
	$(document).ready(function(){
		$("#user_reg_form").submit(function(e){
		    var form = $(this);

			e.preventDefault();
			if($("input[name='password']").val() != $("input[name='password2']").val()){
				alert("Passwords do not match");
				return;
			}

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
						alert(ex);
						return;
					}

					if(jData.errors != null && Object.keys(jData.errors).length > 0)
					{
						str = "";
						$.each(jData.errors,function(index,value){
							str += value+"<br/>";
						});
						alert(str);
					}

					if(jData.success ==1){
						alert("User successfully created");
						location.refresh();
					}


				},
				error : function(){
					alert("Error saving user");
				}
			}).done(function(){
				$(form).children("button[type='submit']").removeAttr("disabled").html("Submit");
			})

		})
	})
        </script>
    @stop
