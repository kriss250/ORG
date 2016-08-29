<p class="text-center">Creating a new announcements</p>
<form action="{{ action("AnnouncementController@store") }}" method="post">
    <input type="hidden" name="_token" value="{{ csrf_token() }}"
    <label>Title</label>
    <input required="" name="title" type="text" class="form-control" />
    <label>Message</label>
    <textarea name="message" class="form-control">

    </textarea>

    <br />

    <input type="submit" class="btn btn-success" value="Send" />
</form>
