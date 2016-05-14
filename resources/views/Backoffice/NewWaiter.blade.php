<h2 style="margin: 0">New Waiter </h2>

<p class="page_info"><i class="fa fa-info-circle"></i> Please use the form below to register new waiters.</p>

<br>
<form name="reg_form" style="max-width:500px;" class="ajax-form" action="{{ action('WaiterController@store')}}" method="post">
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
    <input type="nickname" name="waiter_name" class="form-control" placeholder="Nickname">
 </div>
 

  <button type="submit" class="btn btn-default">Submit</button>
</form>