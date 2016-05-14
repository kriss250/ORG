<h3>Change Password </h3>

<p class="page_info"><i class="fa fa-info-circle"></i> Please use the form below to change your Password providing old password.</p>

<br>
<form  style="max-width:500px;" class="ajax-form" action="{{ action('SettingsController@newPassword')}}" method="post">
<input type="hidden" name="_token" value="{{ csrf_token() }}">

  <div class="form-group">
    <label for="oldpassword">Old Password</label>
    <input type="password" name="oldpassword" autocomplete="off"  class="form-control">
  </div>

  <div class="form-group">
    <label for="password1">New Password</label>
    <input type="password" name="password1" class="form-control" autocomplete="off">
  </div>


<div class="form-group">
    <label for="password2">Repeat new Password</label>
    <input type="password" name="password2" class="form-control">
 </div>
 

  <button type="submit" class="btn btn-success">Submit</button>
</form>