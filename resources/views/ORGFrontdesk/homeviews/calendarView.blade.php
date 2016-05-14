@extends('ORGFrontdesk.homeviews.master')

@section("contents")

<div id='calendar'></div>

<script>

    $('#calendar').fullCalendar({
        theme: true,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        defaultDate: '{{ \ORG\Dates::WORKINGDATE(true,true) }}',
        editable: false,
        eventLimit: false, // allow "more" link when too many events
        events: [
            <?php foreach($data as $room) { ?>

            {
                title: '<?php print($room->room_number." - ". $room->Guest); ?>',
                start: '<?php print($room->checkin); ?>',
                end: '<?php print($room->checkout); ?>',
                backgroundColor: '<?php (strtolower($room->status_name) =='reserved' ? print('#016BE1') : print'#E34747'); ?>'
            },
            
    <?php } ?>
        ]
    });

</script>
@stop 