@extends("Frontdesk::MasterIframe")

@section("contents")

<div class="panel-desc">
    <p class="title">New Day Process</p>
    <p class="desc">Guest without reservation</p>
</div>

<div style="padding:15px;">
Current Date {{\Kris\Frontdesk\Env::WD()->format("d/m/Y")}}
<p class="section-title">
    <span>Day Summary</span>
</p>


<footer>
<p>Make sure you check all room rates , and the total number of guests in house before making a new  day.</p>
<form method="post" action="{{action("\Kris\Frontdesk\Controllers\OperationsController@frame","newDay")}}">
    <input type="hidden" name="_token" value="{{csrf_token()}}" />

    <input class="btn btn-danger" type="submit" value="New Day" />
</form>
    </footer>

<script>
    {{\Session::has('refresh') ? ' window.opener.autoRefresh = true;' : ''}}
    $(document).ready(function(){
    @if(session('msg'))
        alert("{{session('msg')}}");
    @elseif(session('errors'))
        error('{{session("errors")->first()}}');
    @endif
    });

</script>
    </div>
@stop