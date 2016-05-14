<b>Announcements</b>
<br><br>
<table class="table table-bordered">
<thead>
	<tr>
		<th>Sender</th>
		<th>Title</th>
		<th>Message</th>
		<th>Date</th>
	</tr>
</thead>
    @foreach($data as $announcement)
        <tr>
            <td>{{ $announcement->username }}</td>
            <td>{{ $announcement->title }}</td>
            <td>{{ $announcement->body }}</td>
            <td>{{ \App\FX::DT($announcement->date) }}</td>
        </tr>
    @endforeach

</table>