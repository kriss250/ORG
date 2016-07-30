
@extends("Frontdesk::MasterIframe")

@section("contents")

<script>
    function checkRoomsWith(floor,maid)
    {
        $(".room_checkbox.maid_"+maid+":checked").removeAttr("checked").change();
        $(".floor_"+floor+".maid_"+maid+":not(:disabled)").attr("checked","checked").change();

    }

    function disableSimilar(maid,room,src)
    {
        var item = $(".room_"+room).not(".maid_"+maid);

        if($(src).is(":checked"))
        {
            $(item).attr("disabled","disabled");
        }else {
            $(item).removeAttr("disabled");
        }
    }
</script>
<div class="panel-desc">
    <p class="title">Create new Housekeeking Task</p>
    <p class="desc"></p>
</div>

<br />

<?php
$rooms = \Kris\Frontdesk\Room::all();
$floors = \Kris\Frontdesk\Floor::all();
?>
<ul class="nav nav-tabs">
    <?php $maids = \Kris\Frontdesk\Maid::all(); $i = 1;?>
    @foreach( $maids as $maid)

    <li {{$i=="1" ? 'class=active' : ''}}>
        <a href="#{{$i}}" data-toggle="tab">{{explode(" ",$maid->name)[0]}}</a>
    </li>

    <?php $i++; ?>
    @endforeach

    <?php $i = 1;?>


</ul>
<?php
if(isset($_POST['save']))
{
    array_pop($_POST);

    unset($_POST['_token']);

    $keys =array_keys($_POST);
    print_r($keys);
}
?>
<form action="" method="post">
    <input type="hidden" name="_token" value="{{csrf_token()}}" />
<div class="tab-content ">
    
    @foreach( $maids as $maid)
    <div class="tab-pane {{$i==1 ? 'active' : ''}}" id="{{    $i++}}">
        <div class="">
            <fieldset>
                <label>Floor</label>
                <input onchange="" checked data-id="0" name="floor_{{$maid->idmaids}}" type="radio" /><span>None</span>
                @foreach($floors as $floor)
                <input onchange="checkRoomsWith({{$floor->idfloors}},{{$maid->idmaids}})" type="radio" data-id="{{$floor->idfloors}}" name="floor_{{$maid->idmaids}}" /> <span>{{$floor->floor_name}}</span>
                @endforeach
            </fieldset>
        </div>
        @foreach($rooms as $room)
        <fieldset style="display:block;float:left;margin-right:6px;margin-bottom:6px;">
            <label>{{$room->room_number}}</label>
            <input name="room_{{$room->idrooms}}_{{$maid->idmaids}}" onchange="disableSimilar({{$maid->idmaids}},{{$room->idrooms}},this)"  class="room_checkbox room_{{$room->idrooms}} floor_{{$room->floors_id}} maid_{{$maid->idmaids}}" type="checkbox" />
        </fieldset>
        @endforeach
        <div class="clearfix"></div>
    </div>
    @endforeach

</div>
<br />
<button type="submit" name="save" class="btn btn-primary">Save</button>
</form>
<script>
    $(document).ready(function(){
    @if(session('msg'))
        alert("{{session('msg')}}");
    @elseif(session('errors'))
        error('{{session("errors")->first()}}');
    @endif
    });

        initSelectBoxes();

</script>

@stop