<h2 style="margin-top:-5px;">New User </h2>

<p class="page_info"><i class="fa fa-info-circle"></i> Please use the form below to register new users. Assign levels from 1 - 10.</p>

<br>
<form name="reg_form" style="max-width:500px;" class="ajax-form" action="{{ action('UsersController@store')}}" method="post">
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
    <label for="exampleInputEmail1">Username</label>
    <input type="username" name="username" class="form-control" id="exampleInputEmail1" placeholder="Username">
  </div>


<div class="form-group">
    <label for="exampleInputEmail1">Level (1-10) <b style="color:green">Level : <span class="level-preview"></span></b></label>
    <input type="range" onchange="showRangeValue(this)" name="level" data-preview='.level-preview'  step="1" value="1" min="1" max="10" class="form-control" placeholder="">
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