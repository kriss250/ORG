@extends("Frontdesk::MasterIframe")

@section("contents")

<script type="text/javascript">
    initSelectBoxes();
</script>
<style>
    .inline-fieldsets fieldset {
        display: block !important;
        width: 100%;
        background: rgb(253,253,253);
        border-radius: 0;
    }
</style>
<div style="padding:10px 10px;background:#fff" class="inline-fieldsets modal-header">

    <h4>Create A New Room Type</h4>
    <p class="subtitle" style="margin-top:-5px;">Create a new room and assign it to a floor</p>
  
</div>

<div style="overflow:hidden" class="modal-content">
    <form action=""  method="post">
       <input type="hidden" name="_token" value="{{csrf_token()}}" />
        <p>Room Type Name</p>
        <input name="name" type="text" class="form-control" placeholder="E.g Twin" />

        <p>Alias</p>
        <input name="alias" type="text" class="form-control" placeholder="E.g TW" />
 
        <p>&nbsp;</p>
        <input type="submit" value="Save" class="btn btn-success" />
        <p style="margin-bottom:2px">&nbsp;</p>
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