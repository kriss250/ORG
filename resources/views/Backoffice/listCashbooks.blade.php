
	<h4 class="text-center"> Cash Books </h4>
	<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Cashbook</th>
			<th>Balance</th>
			<th> Action</th>
		</tr>
	</thead>
	@foreach($data as $cashbook)
		 <tr>
		 	<td>{{ $cashbook->cashbook_name }}</td>
		 	<td>{{ $cashbook->balance }}</td>
		 	<td><a data-width="630" data-height="520" class="btn btn-xs modal-btn" href="{{ action("CashbookController@show",$cashbook->cashbookid) }}" style="font-size:12px;">Open</a>
		 	 <!-- <a href="{{ action("CashbookController@index","delete") }} style="font-size:10px;color:red">Delete</a> </td> -->
		 </tr>
	@endforeach
	</table>

<a href="{{ action("CashbookController@create") }}" data-height="350" data-width="350" class="btn btn-success modal-btn">New Cashbook</a>