@extends("Frontdesk::MasterIframe")

@section("contents")
@include("Frontdesk::reports.report-filter")

<div class="print-document">
    @include("Frontdesk::reports.report-print-header")
    <table class="table table-condensed table-striped table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>User</th>
                <th style="min-width:350px">Action</th>
            </tr>
        </thead>

        @if(isset($logs))

            @foreach($logs as $log) 
                <tr{!! ($log->type=='danger') ? " class='text-danger' " : "" !!}>
                    <td>{{$log->date}}</td>
                    <td>{{$log->user}}</td>
                    <td>{{$log->action}}</td>
                </tr>
            @endforeach
        @endif
    </table>
   
</div>

@stop
