@extends("Frontdesk::MasterIframe")
@section("contents")

@include("Frontdesk::reports.report-filter")

<div class="print-document">

    <p class="report-title">Banquet Booking Report</p>

    @include("Frontdesk::reports.report-footer")

</div>

@stop

