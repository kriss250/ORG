@extends("Pos.master")

@section("contents")

 <h2>Change Password </h2>

<p class="page_info"><i class="fa fa-info-circle"></i> Please use the form below to change your password providing old password.</p>

<br>
<form name="reg_form" style="max-width:500px;" id="ajaxsave" action="{{ action('SettingsController@newPassword')}}" method="post">
<input type="hidden" name="_token" value="{{ csrf_token() }}">

  <div class="form-group">
    <label for="exampleInputEmail1">Old Password</label>
    <input type="password" name="oldpassword" autocomplete="off"  class="form-control">
  </div>

  <div class="form-group">
    <label for="exampleInputEmail1">New Passwoord</label>
    <input type="password" name="password1" class="form-control" autocomplete="off">
  </div>


<div class="form-group">
    <label for="exampleInputEmail1">New Password</label>
    <input type="password" name="password2" class="form-control">
 </div>
 

  <button type="submit" class="btn btn-default">Submit</button>
</form>


@stop