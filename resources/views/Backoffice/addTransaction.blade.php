<h3 class="text-center">New Transaction</h3>

<form  action="{{ action("CashbookTransactionController@store") }}" method="post">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<label>Motif</label>
<input required="" type="text" class="form-control" name="motif">
<label>Amount</label>
<input required="" type="text" class="form-control" name="amount">
<label>Type</label>
<select required="" name="type" class="form-control">
	<option value="">Choose</option>
	<option value="IN">IN</option>
	<option value="OUT">OUT</option>
</select>

<label>Cashbook</label>
<select required="" name="cashbook" class="form-control">
	<option value="">Choose Cashbook</option>
	@foreach($cashbooks as $cashbook)
	<option value="{{ $cashbook->cashbookid}}">{{ $cashbook->cashbook_name }}</option>
	@endforeach
</select>
<br>
<input class="btn btn-danger" type="submit" name="submit" value="Save">
</form>