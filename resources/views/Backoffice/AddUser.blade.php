<h3>Create new cashbook </h3>
<br>
<form method="post" action="{{ action("CashbookController@store") }}">
	<label>Cashbook Name</label>
	<input type="text" name="bookname" class="form-control"></input>

	<label>Opening Balance</label>
	<input class="form-control" type="text" name="balance">
<br>
	<input type="submit" name="submit" value="Save" class="btn btn-sm btn-success">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
</form>