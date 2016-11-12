
@extends("Frontdesk::MasterIframe")

@section("contents")

<div style="padding:10px 10px;background:#fff" class="modal-header">
    <h4>Create Currency</h4>
    <p class="subtitle" style="margin-top:-5px;">Exchange rate is defined by your local currency</p>
</div>

<form action="" style="padding:15px;background-color:#f9fdff;border:1px solid #b5d9ed;margin-top:-1px" class="form-inline stack-form" method="post">
    <input required name="name" type="text" class="form-control" placeholder="Currency Name" />
    <input required name="alias" type="text" class="form-control" placeholder="Alias" />
    <input type="hidden" name="_token" value="{{csrf_token()}}" />
    <input pattern="[\d\.]+" type="text" name="rate" class="form-control" required placeholder="# Exchange Rate" />
    <input type="submit" class="btn btn-primary" value="Save" />
</form>
<script>
    $(document).ready(function(){
        window.opener.autoRefresh = true;
    @if(session('msg'))
        alert("{{session('msg')}}");
        
    @elseif(session('errors'))
        error('{{session("errors")->first()}}');
    @endif
    });

</script>@stop