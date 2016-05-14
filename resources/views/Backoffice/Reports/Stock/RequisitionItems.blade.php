@if(isset($data) )
<h3>Ref : {{ $data[0]->reference_no }}</h3>
<p>Supplier : {{ $data[0]->supplier_name }}</p>
<table class="table table-bordered table-striped">
	
	<thead>
		<tr>
			<th>Code</th>
			<th>Name</th>
			<th>Qty</th>
			<th>U. Price</th>
			<th>Sub. Total</th>
		</tr>
	</thead>
<?php $GT = 0; ?>

	@foreach($data as $item)
	<tr>
		<td>{{ $item->product_code }}</td>
		<td>{{ $item->product_name }}</td>
		<td>{{ number_format($item->unit_price) }}</td>
		<td>{{ $item->quantity }}</td>
		<td>{{ number_format($item->gross_total) }}</td>
		<?php $GT += $item->gross_total ;?>
	</tr>
	@endforeach

	<tr style="font-weight: bold;">
		<td colspan="4">Total</td><td>{{ number_format($GT) }}</td>
	</tr>

</table>

{!! html_entity_decode($data[0]->note) !!}
@endif