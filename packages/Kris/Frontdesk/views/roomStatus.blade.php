@extends("Frontdesk::MasterIframe")

@section("contents")
<br />

<div class="panel-desc">
    <p class="title">Change Room Status</p>
    <p class="desc">Drag and drop rooms to their respective status</p>
</div>


<script>
    window.opener.autoRefresh = true;
    function allowDrop(ev) {
        ev.preventDefault();

    }

    function drag(ev) {
        ev.dataTransfer.setData("text", ev.target.id);
    }


    function drop(ev) {
        ev.preventDefault();
        var data = ev.dataTransfer.getData("text");
        var destination = null;
        var roomid = data.split("_")[1];

        try {
            if (ev.target.className == "the-room")
            {
                destination = ev.target.parentElement;
            }

            if (ev.target.className.split(" ")[0] == "row")
            {
                destination = ev.target.children[0];
            }

            if (ev.target.className.split(" ")[0].trim() == "rooms-wrapper")
            {
                destination = ev.target;
            }
        }catch(Exception)
        {
            alert(Exception);
        }
        
        
        if (destination != null) {
            var id = $(destination).attr("data-id");
            
            $(destination).append(document.getElementById(data));

            $.ajax({
                type: "get",
                url: "{{action('\Kris\Frontdesk\Controllers\OperationsController@setRoomStatus')}}?roomid="+roomid+"&status="+id,
                success:function()
                {

                }
            })
        }
    }
</script>
<style>
    .vacant-status, 
    .dirty-status, 
    .hu-status, 
    .co-status,
    .blocked-status {
        border:1px solid #ccc;
        padding:15px;
        overflow:auto;
        height:500px;
    }

    .col-xs-2 {
        width:19.8%;
    }
    .vacant-status p {
        border-bottom: 3px solid #0da721;
        margin-bottom: 15px;
    }

    .dirty-status p {
        border-bottom: 3px solid #b0b0b0;
        margin-bottom: 15px;
    }
    .hu-status p {
        border-bottom: 3px solid #ff6a00;
        margin-bottom: 15px;
    }

    .co-status p {
        border-bottom: 3px solid #d50fc5;
        margin-bottom: 15px;
    }

        .blocked-status p {
            border-bottom: 3px solid #282828;
            margin-bottom:15px;
        }

    .the-room {
        display: block;
        background: linear-gradient(to bottom,#fff,#e7e7e7);
        padding: 5px 10px;
        font-size:12px;
        margin-top: 3px;
        border: 1px solid #d1d1d1;
        border-radius: 5px;
        cursor: pointer;
        cursor: grab !important;
    }

    .rooms-wrapper {
        margin-right:-1px;
    }
</style>
<br />
<div class="row text-center">

    <div ondrop="drop(event)" data-id="{{\Kris\Frontdesk\RoomStatus::VACANT}}" ondragover="allowDrop(event)" class="rooms-wrapper col-xs-2 vacant-status">
        <p>Vacant</p>
        <?php
        $vc = \Kris\Frontdesk\Room::where("status",\Kris\Frontdesk\RoomStatus::VACANT)->get();
        $dr = \Kris\Frontdesk\Room::where("status",\Kris\Frontdesk\RoomStatus::DIRTY)->get();
        $hu = \Kris\Frontdesk\Room::where("status",\Kris\Frontdesk\RoomStatus::HOUSEUSE)->get();
        $bl = \Kris\Frontdesk\Room::where("status",\Kris\Frontdesk\RoomStatus::BLOCKED)->get();
        $co = \Kris\Frontdesk\Room::where("status",\Kris\Frontdesk\RoomStatus::CHECKEDOUT)->get();

        ?>

        @foreach($vc as $v)
        <span ondragover="cancelDrop(event)" id="room_{{$v->idrooms}}" ondragstart="drag(event)" draggable="true" class="the-room">{{$v->room_number}} {{$v->type->type_name}}</span>
        @endforeach
    </div>

    <div ondrop="drop(event)" data-id="{{\Kris\Frontdesk\RoomStatus::DIRTY}}" ondragover="allowDrop(event)" class="rooms-wrapper col-xs-2 dirty-status">
        <p>Dirty</p>

        @foreach($dr as $d)
        <span id="room_{{$d->idrooms}}" ondragstart="drag(event)" draggable="true" class="the-room">{{$d->room_number}} {{$d->type->type_name}}</span>
        @endforeach
    </div>

    <div ondrop="drop(event)" ondragover="allowDrop(event)" data-id="{{\Kris\Frontdesk\RoomStatus::HOUSEUSE}}" class="rooms-wrapper col-xs-2 hu-status">
        <p>House Use</p>
        @foreach($hu as $h)
        <span id="room_{{$h->idrooms}}" ondragstart="drag(event)" draggable="true" class="the-room">{{$h->room_number}} {{$h->type->type_name}}</span>
        @endforeach
    </div>

    <div ondrop="drop(event)" data-id="{{\Kris\Frontdesk\RoomStatus::BLOCKED}}" ondragover="allowDrop(event)" class="rooms-wrapper col-xs-2 blocked-status">
        <p>Blocked</p>
        @foreach($bl as $b)
        <span id="room_{{$b->idrooms}}" ondragstart="drag(event)" draggable="true" class="the-room">{{$b->room_number}} {{$b->type->type_name}}</span>
        @endforeach
    </div>

    <div ondrop="drop(event)" data-id="{{\Kris\Frontdesk\RoomStatus::CHECKEDOUT}}" ondragover="allowDrop(event)" class="rooms-wrapper col-xs-2 co-status">
        <p>Checked Out</p>
        @foreach($co as $c)
        <span id="room_{{$c->idrooms}}" ondragstart="drag(event)" draggable="true" class="the-room">{{$c->room_number}} {{$c->type->type_name}}</span>
        @endforeach
    </div>
</div>

@stop