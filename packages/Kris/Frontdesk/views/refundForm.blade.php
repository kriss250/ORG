@extends("Frontdesk::MasterIframe")

@section("contents")
<div class="panel-desc">
    <p class="title">Make a refund</p>
    <p class="desc"></p>
</div>

<script type="text/javascript">
    window.opener.autoRefresh = true;
</script>
<style>
    .inline-fieldsets fieldset{
        display:block !important;
        width:100%;
        background:rgb(253,253,253);
        border-radius:0
    }
</style>
<div style="padding:6px 10px" class="inline-fieldsets">
    <form action="{{action("\Kris\Frontdesk\Controllers\ReservationsController@addRefund",$_GET['id'])}}" method="post">
        <input type="hidden" name="_token" value="{{csrf_token()}}" />
   

        <fieldset class="bordered">
            <label>Amount #</label>
            <input required value="" type="text" name="amount" placeholder="#" />
        </fieldset>

        <fieldset>
            <label>Description</label>
            <input required style="margin-top:6px;padding:4px 5px !important;border-top:1px dashed rgb(215, 215, 215) !important" value="" type="text" name="motif" placeholder="Specify..." />
        </fieldset>

        <input type="submit" value="Save" class="btn btn-success" />
    </form>
</div>

<script>
    $(document).ready(function(){
    @if(session('msg'))
        alert("{{session('msg')}}");
    @elseif(session('errors'))
        error('{{session("errors")->first()}}');
    @endif
    });

</script>
@stop