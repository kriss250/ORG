@extends("Pos.master")

@section("contents")

 <h2>Create a New Table </h2>

<p class="page_info"><i class="fa fa-info-circle"></i> Please use the form below to register new users. Assign levels from 1 - 10.</p>

<br>
<form name="reg_form" style="max-width:500px;" id="user_reg_form" action="{{ action('TableController@store')}}" method="post">
<input type="hidden" name="_token" value="{{ csrf_token() }}">

  <div class="form-group">
    <label for="exampleInputEmail1">Table Name</label>
    <input type="text" name="name"  class="form-control" placeholder="E.g Table 1">
  </div>

  <div class="form-group">
    <label for="exampleInputEmail1">Description</label>
   <textarea class="form-control" name="description"></textarea>
  </div>

  <button type="submit" class="btn btn-default">Submit</button>
</form>


@stop