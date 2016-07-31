@extends("Frontdesk::MasterIframe")

@section("contents")
@include("Frontdesk::reports.report-filter")

<div class="print-document">
    @include("Frontdesk::reports.report-print-header")

                <p class="report-title">Housekeeping Report </p>
    <table class="table table-condensed table-striped table-bordered">
        <thead>
            <tr>
                <th>Room</th>
                <th>Room Type</th>
                <th>Floor</th>
                <th>Maid</th>
            </tr>
        </thead>

        @if(isset($tasks))

            @foreach($tasks as $task) 
                <tr>
                    <td>{{$task->room->room_number}}</td>
                    <td>{{$task->room->type->type_name}}</td>
                    <td>{{$task->room->floor->floor_name}}</td>
                    <td>{{$task->maid->name}}</td>
                </tr>
            @endforeach
        @endif
    </table>
   
</div>

@stop
